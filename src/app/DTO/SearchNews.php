<?php

namespace App\DTO;

class SearchNews
{
    public function __construct(
        public ?string $search = null,
        public int $page = 1
    ) {
    }
}
