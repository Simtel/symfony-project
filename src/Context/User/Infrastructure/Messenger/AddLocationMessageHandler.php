<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Messenger;

use App\Context\User\Domain\Event\LocationAddedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class AddLocationMessageHandler
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(LocationAddedEvent $event): void
    {
        $this->logger->info($event->getMessage());
    }
}
