<?php

namespace App\Events;

use App\Events\Contracts\EventWithMessageInterface;
use App\Models\News;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsAddedEvent implements EventWithMessageInterface
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly News $news)
    {
    }

    public function getMessage(): string
    {
        return sprintf(
            '[%s] %s',
            $this->news->source,
            $this->news->title,
        );
    }

    public function getType(): string
    {
        return 'news_added';
    }

    public function getId(): string
    {
        return $this->news->id;
    }
}
