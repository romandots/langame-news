<?php

namespace App\Services\Users\Exceptions;

use App\Models\User;

class UserConfirmationCodeNotFoundException extends UserRegistrationException
{
    public function __construct(string $message = 'Confirmation code not found. Please, register again.')
    {
        parent::__construct($message);
    }
}
