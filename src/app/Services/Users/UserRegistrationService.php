<?php

namespace App\Services\Users;

use App\DTO\ConfirmationCode;
use App\DTO\User as UserDto;
use App\Models\User;
use App\Notifications\RegistrationConfirmationCodeNotification;
use App\Repositories\UserRepository;
use App\Services\Users\Exceptions\UserConfirmationCodeExpiredException;
use App\Services\Users\Exceptions\UserRegistrationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

readonly class UserRegistrationService
{

    protected int $confirmationCodeLength;
    protected int $confirmationCodeLifetime;

    public function __construct(
        protected UserRepository $userRepository,
        protected LoggerInterface $logger,
    ) {
        $this->confirmationCodeLength = (int)config('registration.confirmation_code.length', 6);
        $this->confirmationCodeLifetime = (int)config('registration.confirmation_code.expires_at', 60);
    }

    public function registerUser(UserDto $userDto): void
    {
        DB::beginTransaction();
        try {
            $user = $this->createUser($userDto);
            $this->sendNewConfirmationCode($user);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger->error('User registration failed', [
                'user' => $userDto,
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
        DB::commit();
        Auth::login($user);
    }

    /**
     * @param User $user
     * @param string $code
     * @return void
     * @throws UserConfirmationCodeExpiredException|UserRegistrationException
     */
    public function confirmUser(User $user, string $code): void
    {
        try {
            $this->checkConfirmationCode($user, $code);
        } catch (UserConfirmationCodeExpiredException $exception) {
            // Code expired - send new code
            $this->sendNewConfirmationCode($exception->user);
            throw $exception;
        }
    }

    protected function createUser(UserDto $userDto): User
    {
        $user = $this->userRepository->create($userDto);
        $this->logger->debug('User created', ['user' => $user]);

        return $user;
    }

    public function sendNewConfirmationCode(User $user): void
    {
        $confirmationCode = new ConfirmationCode(
            code: Str::password(length: $this->confirmationCodeLength, letters: false, numbers: true, symbols: false),
            expiresAt: now()->addSeconds($this->confirmationCodeLifetime),
            isConfirmed: false
        );
        $this->userRepository->updateConfirmation($user, $confirmationCode);
        $this->logger->debug('New confirmation code set', ['user' => $user]);

        $user->notify(new RegistrationConfirmationCodeNotification($user->confirmation_code));
        $this->logger->info('Confirmation code sent', ['user_id' => $user->id, 'code' => $user->confirmation_code]);
    }

    protected function checkConfirmationCode(User $user, string $code): User
    {
        if (!$user->confirmation_code || !$user->confirmation_code_expires_at || $user->confirmation_code_expires_at->isPast()) {
            $this->logger->debug('Confirmation code expired or not set.', ['user_id' => $user->id, 'code' => $code]);
            throw new UserConfirmationCodeExpiredException($user);
        }

        $this->userRepository->updateConfirmation(
            $user,
            new ConfirmationCode(
                code: null,
                expiresAt: null,
                isConfirmed: true
            )
        );
        $this->logger->debug('Confirmation code is correct. User registration complete.', ['user_id' => $user->id, 'code' => $code]);

        return $user;
    }

}
