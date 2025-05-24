<?php

namespace App\Services\News\Contracts;

use App\DTO\News;
use App\Services\News\Exceptions\NewsServiceConnectionException;
use App\Services\News\Exceptions\NewsServiceException;

interface NewsAggregatorInterface
{
    /**
     * Fetch news from the source
     *
     * @param \DateTime $offset Skip news older than this date
     * @return News[]
     * @throws NewsServiceConnectionException
     * @throws NewsServiceException
     */
    public function getNews(\DateTime $offset): array;

    public function getSource(): string;
}
