<?php

namespace App\Services\Users;

use App\DTO\ConfirmationCode;
use App\DTO\User as UserDto;
use App\Models\User;
use App\Notifications\RegistrationConfirmationCodeNotification;
use App\Repositories\UserRepository;
use App\Services\Users\Exceptions\UserConfirmationCodeExpiredException;
use App\Services\Users\Exceptions\UserConfirmationCodeNotFoundException;
use App\Services\Users\Exceptions\UserRegistrationRedirectWithErrorException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
    }

    /**
     * @param string $code
     * @return void
     * @throws UserRegistrationRedirectWithErrorException
     */
    public function confirmUser(string $code): void
    {
        try {
            $user = $this->checkConfirmationCode($code);
        } catch (UserConfirmationCodeExpiredException $exception) {
            // Code expired - send new code
            $this->sendNewConfirmationCode($exception->user);
            throw new UserRegistrationRedirectWithErrorException($exception->getMessage());
        } catch (UserConfirmationCodeNotFoundException $exception) {
            // Code not found - redirect to registration form
            // @todo Implement the recovery mechanism for lost session
            throw new UserRegistrationRedirectWithErrorException($exception->getMessage(), 'register');
        } catch (\Exception $exception) {
            throw new UserRegistrationRedirectWithErrorException($exception->getMessage());
        }

        Auth::login($user);
    }

    protected function createUser(\App\DTO\User $userDto): User
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

        Session::put('confirmation_user_id', $user->id);

        $user->notify(new RegistrationConfirmationCodeNotification($user->confirmation_code));
        $this->logger->info('Confirmation code sent', ['user_id' => $user->id, 'code' => $user->confirmation_code]);
    }

    protected function checkConfirmationCode(string $code): User
    {
        $userId = Session::get('confirmation_user_id');
        if (!$userId) {
            $this->logger->error('No user ID found in session for confirmation code check.', ['code' => $code]);
            throw new UserConfirmationCodeNotFoundException();
        }
        try {
            $user = $this->userRepository->findById((int)$userId);
        } catch (\Exception $e) {
            Session::forget('confirmation_user_id');
            $this->logger->error('User not found for confirmation code check.', ['user_id' => $userId, 'code' => $code, 'exception' => $e]);
            throw new UserConfirmationCodeNotFoundException();
        }

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

        Session::forget('confirmation_user_id');

        return $user;
    }

}
