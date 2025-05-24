<?php

namespace App\Services\News\Exceptions;

use App\Services\News\Exceptions\NewsServiceException;

class NewsServiceConfigException extends NewsServiceException
{
    public function __construct(
        string $message = "Invalid configuration for the news service",
        int $code = 500,
        \Throwable $previous = null
    ) {
        parent::__construct($message, [], $code, $previous);
    }
}
