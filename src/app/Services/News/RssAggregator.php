<?php

namespace App\Services\News;

use Illuminate\Contracts\Container\BindingResolutionException;
use SimplePie\SimplePie;
use Vedmant\FeedReader\FeedReader;

abstract class RssAggregator
{

    public function __construct(protected FeedReader $feedReader)
    {
    }

    abstract public function getFeedUrl(): string;

    /**
     * Configuration preset name from config/feed-reader.php
     *
     * @return string
     */
    public function getFeedConfiguration(): string
    {
        return 'default';
    }

    /**
     * Returns the cURL options for the feed reader
     * for authentication or other purposes.
     *
     * @return array
     */
    public function getCurlOptions(): array
    {
        return [
            // CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
            // CURLOPT_USERPWD => 'username:password',
        ];
    }

    /**
     * Reads the RSS feed and returns a SimplePie instance.
     *
     * @return SimplePie
     * @throws BindingResolutionException
     */
    public function readFeed(): SimplePie
    {
        return $this->feedReader->read($this->getFeedUrl(), $this->getFeedConfiguration(), [
            'curl_options' => $this->getCurlOptions(),
        ]);
    }
}
