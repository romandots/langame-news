<?php

namespace App\Services\Users;

use App\DTO\LoginCredentials;
use Psr\Log\LoggerInterface;

class UserSessionService
{

    public function __construct(
        protected LoggerInterface $logger,
    ) { }

    public function login(LoginCredentials $credentials): bool
    {
        $isSuccess = auth()->attempt([
            'email' => $credentials->email,
            'password' => $credentials->password,
        ], $credentials->remember);

        if ($isSuccess) {
            $user = auth()->user();
            session()->regenerate();
            $this->logger->debug('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return true;
        }

        $this->logger->warning('Login attempt failed', [
            'email' => $credentials->email,
        ]);

        return false;
    }

    public function logout(): void
    {
        $user = auth()->user();

        if (!$user) {
            $this->logger->warning('Logout attempted without an authenticated user');
            return;
        }

        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->logger->debug('User logged out successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
