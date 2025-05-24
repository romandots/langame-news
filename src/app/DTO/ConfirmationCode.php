<?php

namespace App\DTO;

class ConfirmationCode
{
    public function __construct(
        public readonly ?string $code,
        public ?\DateTime $expiresAt,
        public bool $isConfirmed,
    ) {
    }
}
