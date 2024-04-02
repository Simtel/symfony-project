<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Event;

final readonly class LocationAddedEvent
{
    public function __construct(private string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }


}
