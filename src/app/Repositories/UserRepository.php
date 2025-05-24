<?php

namespace App\Repositories;

use App\DTO\ConfirmationCode;
use App\DTO\User as UserDto;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function create(UserDto $user): UserModel
    {
        return UserModel::create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => Hash::make($user->password),
        ]);
    }

    public function updateConfirmation(UserModel $user, ConfirmationCode $confirmationCode): void
    {
        $user->confirmation_code = $confirmationCode->code;
        $user->confirmation_code_expires_at = $confirmationCode->expiresAt;
        $user->is_confirmed = $confirmationCode->isConfirmed;
        $user->save();
    }

    /**
     * @param int $userId
     * @return UserModel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     */
    public function findById(int $userId): UserModel
    {
        return UserModel::query()->findOrFail($userId);
    }
}
