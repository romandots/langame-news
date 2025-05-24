<?php

namespace App\Services\News\Exceptions;

class NewsServiceConnectionException extends NewsServiceException
{
    public function __construct(
        string $message = "Failed to connect to the news service",
        array $context = [],
        int $code = 500,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $context, $code, $previous);
    }
}
