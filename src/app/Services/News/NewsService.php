<?php

namespace App\Services\News;

use App\DTO\CollectionResponse;
use App\DTO\SearchNews;
use App\Events\NewsAddedEvent;
use App\Repositories\NewsRepository;
use App\Services\News\Contracts\NewsAggregatorInterface;
use Psr\Log\LoggerInterface;

readonly class NewsService
{
    public function __construct(
        private NewsRepository $newsRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function aggregateNews(NewsAggregatorInterface $newsAggregator): int
    {
        $dateOffset = $this->getLastPublishedDateForSource($newsAggregator->getSource());

        try {
            $news = $newsAggregator->getNews($dateOffset);
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch news from aggregator', [
                'source' => $newsAggregator->getSource(),
                'error_message' => $e->getMessage(),
            ]);
            return 0;
        }

        $counter = 0;
        foreach ($news as $item) {
            try {
                $record = $this->newsRepository->create($item);
                $counter++;
                event(new NewsAddedEvent($record));
            } catch (\Exception $e) {
                $this->logger->error('Failed to save news item', [
                    'source' => $item->source,
                    'title' => $item->title,
                    'url' => $item->url,
                    'data' => (array)$item,
                    'error_message' => $e->getMessage(),
                ]);
                continue;
            }
        }

        return $counter;
    }

    private function getLastPublishedDateForSource(string $source): \DateTime
    {
        return $this->newsRepository->getLastPublishedDateForSource($source) ?? new \DateTime('now -1 month');
    }

    public function search(SearchNews $searchNews): CollectionResponse
    {
        $itemsPerPage = (int)config('news.search.items_per_page', 10);
        ['items' => $items, 'total' => $total] = $this->newsRepository->search(
            $searchNews->search,
            $searchNews->page,
            $itemsPerPage
        );
        $items->transform(function ($news) {
            return $news->toArray() + [
                    'html' => view('news.entry', ['news' => $news])->render(),
                ];
        });

        $this->logger->debug('News search performed', [
            'search_term' => $searchNews->search,
            'page' => $searchNews->page,
            'items_per_page' => $itemsPerPage,
            'results_count' => $total,
        ]);

        return new CollectionResponse(
            data: $items->toArray(),
            last_page: (int)ceil($total / $itemsPerPage),
            current_page: $searchNews->page,
        );
    }
}
