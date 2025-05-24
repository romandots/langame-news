<?php

namespace Tests\Unit\Services\News;

use App\DTO\News;
use App\Services\News\NewsService;
use App\Services\News\Contracts\NewsAggregatorInterface;
use App\Repositories\NewsRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NewsServiceTest extends TestCase
{
    public function testAggregateNewsSuccess(): void
    {
        $newsItem = new News(
            title: 'Test Title',
            summary: 'Test Summary',
            description: 'Test Description',
            url: 'http://example.com',
            source: 'test_source',
            published_at: new \DateTime('2023-10-01 12:00:00'),
            ext_id: '12345',
        );

        $newsAggregator = $this->createMock(NewsAggregatorInterface::class);
        $newsAggregator->method('getSource')->willReturn('test_source');
        $newsAggregator->method('getNews')->willReturn([$newsItem]);

        $newsRepository = $this->createMock(NewsRepository::class);
        $newsRepository->method('getLastPublishedDateForSource')->willReturn(null);
        $newsRepository->expects($this->once())->method('create')->with($newsItem);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $service = new NewsService($newsRepository, $logger);

        $result = $service->aggregateNews($newsAggregator);

        $this->assertEquals(1, $result);
    }

    public function testAggregateNewsAggregatorThrowsException(): void
    {
        $newsAggregator = $this->createMock(NewsAggregatorInterface::class);
        $newsAggregator->method('getSource')->willReturn('test_source');
        $newsAggregator->method('getNews')->willThrowException(new \Exception('Aggregator error'));

        $newsRepository = $this->createMock(NewsRepository::class);
        $newsRepository->method('getLastPublishedDateForSource')->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')
            ->with(
                $this->stringContains('Failed to fetch news from aggregator'),
                $this->arrayHasKey('error_message')
            );

        $service = new NewsService($newsRepository, $logger);

        $result = $service->aggregateNews($newsAggregator);

        $this->assertEquals(0, $result);
    }

    public function testAggregateNewsRepositoryThrowsException(): void
    {
        $newsItem = new News(
            title: 'Test Title',
            summary: 'Test Summary',
            description: 'Test Description',
            url: 'http://example.com',
            source: 'test_source',
            published_at: new \DateTime('2023-10-01 12:00:00'),
            ext_id: '12345',
        );

        $newsAggregator = $this->createMock(NewsAggregatorInterface::class);
        $newsAggregator->method('getSource')->willReturn('test_source');
        $newsAggregator->method('getNews')->willReturn([$newsItem]);

        $newsRepository = $this->createMock(NewsRepository::class);
        $newsRepository->method('getLastPublishedDateForSource')->willReturn(null);
        $newsRepository->method('create')->willThrowException(new \Exception('DB error'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')
            ->with(
                $this->stringContains('Failed to save news item'),
                $this->arrayHasKey('error_message')
            );

        $service = new NewsService($newsRepository, $logger);

        $result = $service->aggregateNews($newsAggregator);

        $this->assertEquals(0, $result);
    }

    public function testAggregateNewsEmpty(): void
    {
        $newsAggregator = $this->createMock(NewsAggregatorInterface::class);
        $newsAggregator->method('getSource')->willReturn('test_source');
        $newsAggregator->method('getNews')->willReturn([]);

        $newsRepository = $this->createMock(NewsRepository::class);
        $newsRepository->method('getLastPublishedDateForSource')->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $service = new NewsService($newsRepository, $logger);

        $result = $service->aggregateNews($newsAggregator);

        $this->assertEquals(0, $result);
    }
}
