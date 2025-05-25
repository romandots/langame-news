<?php

namespace App\Events;

use App\Events\Contracts\EventWithMessageInterface;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserConfirmedEvent implements EventWithMessageInterface
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly User $user)
    {
    }

    public function getMessage(): string
    {
        return "Пользователь {$this->user->name} подтвердил регистрацию";
    }

    public function getType(): string
    {
        return 'user_confirmed';
    }

    public function getId(): string
    {
        return $this->user->id;
    }
}
