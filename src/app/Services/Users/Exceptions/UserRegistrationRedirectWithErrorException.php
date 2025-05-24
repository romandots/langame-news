<?php

namespace App\Services\Users\Exceptions;

class UserRegistrationRedirectWithErrorException extends UserRegistrationException
{

    public readonly ?string $redirectRoute;

    public function __construct(string $message = 'An error occurred during user registration.', ?string $redirectRoute = null)
    {
        parent::__construct($message);
        $this->redirectRoute = $redirectRoute;
    }
}
