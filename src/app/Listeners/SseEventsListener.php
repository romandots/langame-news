<?php

namespace App\Listeners;

use App\Events\Contracts\EventWithMessageInterface;
use App\Services\Push\PushService;

class SseEventsListener
{

    public function __construct(
        protected PushService $service
    ) {
    }

    public function handle(EventWithMessageInterface $event): void
    {
        $this->service->pushToEveryone($event);
    }
}
