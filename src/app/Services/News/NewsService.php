<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsAggregatorInterface;
use App\Repositories\NewsRepository;
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
                $this->newsRepository->create($item);
                $counter++;
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
}
