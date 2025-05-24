<?php

namespace App\DTO;

readonly class User
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
    ) {
    }
}
