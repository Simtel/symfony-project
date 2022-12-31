<?php

declare(strict_types=1);

namespace App\Context\User\Application\CommandHandler;

use App\Context\Common\Infrastructure\Contract\CommandHandlerInterface;
use App\Context\User\Application\Command\UpdateUserCommand;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class UpdateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $dto = $command->getDto();
        $user = $dto->getUser();

        foreach ($dto->getLocations() as $location) {
            $user->addLocation($location);
        }

        $events = $user->getEvents();
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
