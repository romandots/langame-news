<?php

namespace App\Events;

use App\Events\Contracts\EventWithMessageInterface;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredEvent implements EventWithMessageInterface
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly User $user)
    {
    }

    public function getMessage(): string
    {
        return "Пользователь {$this->user->name} зарегистрировался";
    }

    public function getType(): string
    {
        return 'user_registered';
    }

    public function getId(): string
    {
        return $this->user->id;
    }
}
