<?php

namespace App\Repositories;

use App\DTO\ConfirmationCode;
use App\DTO\User as UserDto;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * @param int $page
     * @param int $itemsPerPage
     * @return array{items: Collection, total: int}
     */
    public function search(int $page, int $itemsPerPage): array
    {
        $query = UserModel::query();
        $total = $query->count();
        $lastPage = (int)ceil($total / $itemsPerPage);
        $page = min($page, $lastPage);

        $items = $query
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $itemsPerPage)
            ->take($itemsPerPage)
            ->get();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
