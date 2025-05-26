<?php

namespace App\Services\News\NewsAggregators;

use App\DTO\News;
use App\Services\News\Contracts\NewsAggregatorInterface;
use App\Services\News\Exceptions\NewsServiceConfigException;
use App\Services\News\Exceptions\NewsServiceConnectionException;
use App\Services\News\RssAggregator;
use Illuminate\Http\Client\PendingRequest as Http;
use Psr\Log\LoggerInterface;
use SimplePie\Item;

class UnNewsRssAggregator extends RssAggregator implements NewsAggregatorInterface
{

    public const SOURCE = 'UN News';
    public string $url;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct(app('feed-reader'));
        $this->url = (string)config('news.sources.un_news.url');
        if (empty($this->url)) {
            throw new NewsServiceConfigException('UN News URL is not set in the configuration.');
        }
    }

    /**
     * @inheritDoc
     */
    public function getNews(\DateTime $offset): array
    {
        try {
            $feed = $this->readFeed();
        } catch (\Exception $e) {
            $this->logger->error('Failed to read UN News RSS feed', [
                'url' => $this->getFeedUrl(),
                'error_message' => $e->getMessage(),
            ]);
            throw new NewsServiceConnectionException('Failed to read UN News RSS feed.', 500, $e);
        }

        $rawData = $feed->get_items();
        if (empty($rawData)) {
            return [];
        }

        $news = [];
        foreach ($rawData as $item) {
            try {
                $this->validateItem($item);
            } catch (\Exception $e) {
                $this->logger->error('Invalid item format from UN News', [
                    'raw' => (array)$item,
                    'error_message' => $e->getMessage(),
                ]);
                continue;
            }

            $publishedAt = $item->get_date('Y-m-d H:i:s');
            if (strtotime($publishedAt) <= $offset->getTimestamp()) {
                continue; // Skip items older than the offset
            }
            $news[] = $this->mapToNewsDto($item);
        }

        return $news;
    }

    protected function mapToNewsDto(Item $item): News
    {
        return new News(
            title: $item->get_title(),
            summary: $item->get_description(),
            description: $item->get_description(),
            url: $item->get_link(),
            source: $this->getSource(),
            published_at: new \DateTime($item->get_date('Y-m-d H:i:s')),
            ext_id: $item->get_id(),
        );
    }

    protected function validateItem(Item $item): void
    {
        if (empty($item->get_title())) {
            throw new \InvalidArgumentException('Item title is required.');
        }

        if (empty($item->get_link())) {
            throw new \InvalidArgumentException('Item link is required.');
        }

        if (empty($item->get_date('Y-m-d H:i:s'))) {
            throw new \InvalidArgumentException('Item date is required.');
        }

        if (empty($item->get_description())) {
            throw new \InvalidArgumentException('Item description is required.');
        }

        try {
            new \DateTime($item->get_date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid date format in 'pubDate' field", 0, $e);
        }
    }

    public function getSource(): string
    {
        return self::SOURCE;
    }

    public function getFeedUrl(): string
    {
        return $this->url;
    }

}
