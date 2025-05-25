<?php

namespace App\Events\Contracts;

interface EventWithMessageInterface
{
    public function getMessage(): string;

    public function getType(): string;

    public function getId(): string;
}
