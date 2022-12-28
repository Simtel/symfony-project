<?php

declare(strict_types=1);

namespace App\Context\User\Application\CommandHandler;

use App\Context\Common\Infrastructure\Contract\CommandHandlerInterface;
use App\Context\User\Application\Command\UpdateUserCommand;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    public function __invoke(UpdateUserCommand $command): void
    {
        $dto = $command->getDto();
        $user = $dto->getUser();

        foreach ($dto->getLocations() as $location) {
            $user->addLocation($location);
        }
    }
}
