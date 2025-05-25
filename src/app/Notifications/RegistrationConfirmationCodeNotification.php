<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class RegistrationConfirmationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|object|null
     */
    public string $chatId;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly int $code)
    {
        $this->chatId = config('services.telegram-bot-api.chat_id');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }


    /**
     * Get the Telegram representation of the notification.
     *
     * @param object $notifiable
     * @return TelegramMessage
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->to($this->chatId)
            ->line("Добро пожаловать, {$notifiable->name}!")
            ->line("Для завершения регистрации введите код подтверждения: {$this->code}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->code,
            'message' => "Ваш код подтверждения: {$this->code}",
        ];
    }
}
