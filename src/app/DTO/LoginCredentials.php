<?php

namespace App\DTO;

readonly class LoginCredentials
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {
    }
}
