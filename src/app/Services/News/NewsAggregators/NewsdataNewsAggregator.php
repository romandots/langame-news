<?php

namespace App\Services\News\NewsAggregators;

use App\DTO\News;
use App\Services\News\Contracts\NewsAggregatorInterface;
use App\Services\News\Exceptions\NewsServiceConfigException;
use App\Services\News\Exceptions\NewsServiceConnectionException;
use App\Services\News\Exceptions\NewsServiceException;
use Illuminate\Http\Client\PendingRequest as Http;
use Psr\Log\LoggerInterface;

class NewsdataNewsAggregator implements NewsAggregatorInterface
{
    public const SOURCE = 'Newsdata.io';
    public string $url;

    public function __construct(
        private readonly Http $http,
        private readonly LoggerInterface $logger,
    ) {
        $this->url = (string)config('news.sources.newsdata.url');
        if (empty($this->url)) {
            throw new NewsServiceConfigException('Newsdata.io URL is not set in the configuration.');
        }
    }

    public function getSource(): string
    {
        return self::SOURCE;
    }

    /**
     * @return News[]
     * @throws NewsServiceConnectionException
     * @throws NewsServiceException
     */
    public function getNews(\DateTime $offset): array
    {
        $rawData = $this->fetchNewsData();
        if (empty($rawData)) {
            return [];
        }

        $news = [];
        foreach ($rawData as $item) {
            try {
                $this->validateItem($item);
            } catch (\Exception $e) {
                $this->logger->error('Invalid item format from Newsdata.io', [
                    'raw' => $item,
                    'exception' => $e,
                ]);
                continue;
            }

            $dto = $this->mapToNewsDto($item);
            if ($dto->published_at > $offset) {
                $news[] = $dto;
            }
        }

        return $news;
    }

    private function fetchNewsData(): array
    {
        try {
            $response = $this->http->get($this->url);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new NewsServiceConnectionException('Failed to fetch news data from Newsdata.io', [
                'url' => $this->url,
            ], 500, $e);
        }

        if ($response->status() !== 200) {
            throw new NewsServiceException('Failed to fetch news data from Newsdata.io', [
                'status' => $response->status(),
                'response' => $response->body(),
            ], $response->status());
        }

        $responseData = $response->json();
        if (!is_array($responseData) || !isset($responseData['status'], $responseData['results'])
            || $responseData['status'] !== 'success' || !is_array($responseData['results'])) {
            throw new NewsServiceException('Invalid response format from Newsdata.io', [
                'response' => $response->body(),
            ], 500);
        }

        return $responseData['results'];
    }

    private function validateItem(array $item): void
    {
        if (!isset($item['title'])) {
            throw new \InvalidArgumentException("Missing required field 'title' in news item");
        }

        if (!isset($item['link'])) {
            throw new \InvalidArgumentException("Missing required field 'link' in news item");
        }

        if (!isset($item['description'])) {
            throw new \InvalidArgumentException("Missing required field 'description' in news item");
        }

        if (!isset($item['content'])) {
            throw new \InvalidArgumentException("Missing required field 'content' in news item");
        }

        if (!isset($item['article_id'])) {
            throw new \InvalidArgumentException("Missing required field 'article_id' in news item");
        }

        if (!isset($item['pubDate'])) {
            throw new \InvalidArgumentException("Missing required field 'pubDate' in news item");
        }

        try {
            new \DateTime($item['pubDate']);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid date format in 'pubDate' field", 0, $e);
        }
    }

    private function mapToNewsDto(array $item): News
    {
        return new News(
            title: (string)$item['title'],
            summary: (string)$item['description'],
            description: (string)$item['content'],
            url: (string)$item['link'],
            source: $this->getSource(),
            published_at: new \DateTime($item['pubDate']),
            ext_id: (string)$item['article_id'],
        );
    }
}
