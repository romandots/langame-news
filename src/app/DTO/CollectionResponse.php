<?php

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;

readonly class CollectionResponse
{
    public function __construct(
        public array $data,
        public int $last_page,
        public int $current_page,
    ) {
    }
}
