<?php

namespace App\DTO;

readonly class News
{
    public function __construct(
        public string $title,
        public string $summary,
        public string $description,
        public string $url,
        public string $source,
        public \DateTime $published_at,
        public ?string $ext_id = null,
    ) {
    }
}
