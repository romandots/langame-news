<?php

namespace App\Services\Users\Exceptions;

class UserConfirmationCodeNotFoundException extends UserRegistrationException
{
    public function __construct(string $message = 'Confirmation code not found. Please, register again.')
    {
        parent::__construct($message);
    }
}
