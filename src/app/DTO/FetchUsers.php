<?php

namespace App\DTO;

readonly class FetchUsers
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
    }
}
