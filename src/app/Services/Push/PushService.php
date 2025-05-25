<?php

namespace App\Services\Push;

use App\Events\Contracts\EventWithMessageInterface;
use App\Repositories\UserRepository;
use Psr\Log\LoggerInterface;

class PushService
{

    protected const SSE_EVENTS_KEY = 'sse_events_';

    public function __construct(
        protected UserRepository $userRepository,
        protected LoggerInterface $logger,
    ) {
    }

    public function pushToEveryone(EventWithMessageInterface $event): void
    {
        $logPayload = [
            'event_id' => $event->getId(),
            'event_type' => $event->getType(),
            'message' => $event->getMessage(),
        ];
        $this->logger->debug('Pushing event to all users', $logPayload);

        $users = $this->userRepository->getAllConfirmedUsers();
        foreach ($users as $user) {
            $this->pushToUser($user->id, [
                'id' => $event->getId(),
                'type' => $event->getType(),
                'data' => [
                    'message' => $event->getMessage(),
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            $this->logger->debug('Event pushed to user queue', $logPayload + ['user_id' => $user->id]);
        }
    }

    public function getUserEvents(int $userId): array
    {
        return cache()->pull($this->getCacheKey($userId), []);
    }

    protected function pushToUser(int $userId, array $event): void
    {
        $events = $this->getUserEvents($userId);
        $events[] = $event;
        cache()->put($this->getCacheKey($userId), $events, 60);
    }

    protected function getCacheKey(int $userId): string
    {
        return self::SSE_EVENTS_KEY . $userId;
    }
}
