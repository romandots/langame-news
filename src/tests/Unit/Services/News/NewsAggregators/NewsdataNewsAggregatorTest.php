<?php

namespace Tests\Unit\Services\News\NewsAggregators;

use App\DTO\News;
use App\Services\News\Exceptions\NewsServiceConfigException;
use App\Services\News\Exceptions\NewsServiceConnectionException;
use App\Services\News\Exceptions\NewsServiceException;
use App\Services\News\NewsAggregators\NewsdataNewsAggregator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NewsdataNewsAggregatorTest extends TestCase
{
    use PHPMock;

    private PendingRequest $http;
    private LoggerInterface $logger;
    public static array $config = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = $this->createMock(PendingRequest::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->getFunctionMock('App\Services\News\NewsAggregators', 'config')
            ->expects($this->any())
            ->willReturnCallback(fn($key) => self::$config[$key] ?? null);
    }

    public function testThrowsExceptionIfUrlNotSet(): void
    {
        self::$config['news.sources.newsdata.url'] = '';
        $this->expectException(NewsServiceConfigException::class);
        new NewsdataNewsAggregator($this->http, $this->logger);
    }

    public function testGetNewsReturnsNewsArray(): void
    {
        self::$config['news.sources.newsdata.url'] = 'http://fake.url/api';
        $responseData = [
            'status' => 'success',
            'results' => [
                [
                    'title' => 'Test Title',
                    'description' => 'Test Summary',
                    'content' => 'Test Content',
                    'link' => 'http://test/link',
                    'article_id' => '123',
                    'pubDate' => '2025-05-24 12:00:00',
                ],
            ],
        ];

        $this->mockRequest($responseData);

        $aggregator = new NewsdataNewsAggregator($this->http, $this->logger);
        $offset = new \DateTime('2025-05-23 12:00:00');
        $news = $aggregator->getNews($offset);

        $this->assertCount(1, $news);
        $this->assertInstanceOf(News::class, $news[0]);
        $this->assertEquals('Test Title', $news[0]->title);
    }

    public function testGetNewsFiltersByOffset(): void
    {
        $responseData = [
            'status' => 'success',
            'results' => [
                [
                    'title' => 'Old News',
                    'description' => 'Old',
                    'content' => 'Old Content',
                    'link' => 'http://test/old',
                    'article_id' => '1',
                    'pubDate' => '2025-01-01 00:00:00',
                ],
                [
                    'title' => 'New News',
                    'description' => 'New',
                    'content' => 'New Content',
                    'link' => 'http://test/new',
                    'article_id' => '2',
                    'pubDate' => '2025-05-01 00:00:00',
                ],
            ],
        ];

        $this->mockRequest($responseData);

        $aggregator = new NewsdataNewsAggregator($this->http, $this->logger);
        $offset = new \DateTime('2025-04-30 00:00:00');
        $news = $aggregator->getNews($offset);

        $this->assertCount(1, $news);
        $this->assertEquals('New News', $news[0]->title);
    }

    public function testGetNewsSkipsInvalidItemsAndLogsError(): void
    {
        $responseData = [
            'status' => 'success',
            'results' => [
                [
                    // отсутствует title
                    'description' => 'No title',
                    'content' => 'Content',
                    'link' => 'http://test/invalid',
                    'article_id' => '3',
                    'pubDate' => '2024-06-01T00:00:00Z',
                ],
                [
                    'title' => 'Valid News',
                    'description' => 'Valid',
                    'content' => 'Valid Content',
                    'link' => 'http://test/valid',
                    'article_id' => '4',
                    'pubDate' => '2024-06-02T00:00:00Z',
                ],
            ],
        ];

        $this->mockRequest($responseData);
        $this->logger->expects($this->once())->method('error')->with(
            $this->stringContains('Invalid item format from Newsdata.io'),
            $this->arrayHasKey('raw')
        );

        $aggregator = new NewsdataNewsAggregator($this->http, $this->logger);
        $offset = new \DateTime('2024-05-01T00:00:00Z');
        $news = $aggregator->getNews($offset);

        $this->assertCount(1, $news);
        $this->assertEquals('Valid News', $news[0]->title);
    }

    public function testFetchNewsDataThrowsConnectionException(): void
    {
        $this->http->method('get')->willThrowException(new ConnectionException('fail'));

        $aggregator = new NewsdataNewsAggregator($this->http, $this->logger);

        $this->expectException(NewsServiceConnectionException::class);

        $reflection = new \ReflectionClass($aggregator);
        $method = $reflection->getMethod('fetchNewsData');
        $method->setAccessible(true);
        $method->invoke($aggregator);
    }

    public function testFetchNewsDataThrowsServiceExceptionOnBadStatus(): void
    {
        $this->mockRequest(['status' => 'error'], 500);

        $aggregator = new NewsdataNewsAggregator($this->http, $this->logger);

        $this->expectException(NewsServiceException::class);

        $reflection = new \ReflectionClass($aggregator);
        $method = $reflection->getMethod('fetchNewsData');
        $method->setAccessible(true);
        $method->invoke($aggregator);
    }

    public function testFetchNewsDataThrowsServiceExceptionOnInvalidFormat(): void
    {
        $this->mockRequest(['foo' => 'bar'], 200);
        $aggregator = new NewsdataNewsAggregator($this->http, $this->logger);

        $this->expectException(NewsServiceException::class);

        $reflection = new \ReflectionClass($aggregator);
        $method = $reflection->getMethod('fetchNewsData');
        $method->setAccessible(true);
        $method->invoke($aggregator);
    }

    protected function mockRequest(array $responseData, int $statusCode = 200): void
    {
        $response = $this->createMock(Response::class);
        $response->method('status')->willReturn($statusCode);
        $response->method('json')->willReturn($responseData);
        $this->http->method('get')->willReturn($response);
    }
}
