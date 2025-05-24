<?php

namespace App\Services\News\Exceptions;

class NewsServiceException extends \Exception
{
    public readonly array $context;

    public function __construct(
        string $message = "An error occurred in the news service",
        array $context = [],
        int $code = 500,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
}
