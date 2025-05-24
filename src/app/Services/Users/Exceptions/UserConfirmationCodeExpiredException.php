<?php

namespace App\Services\Users\Exceptions;

use App\Models\User;

class UserConfirmationCodeExpiredException extends UserRegistrationException
{
    public function __construct(public readonly User $user, string $message = 'Confirmation code has expired. Try again.')
    {
        parent::__construct($message);
    }
}
