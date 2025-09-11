<?php

declare(strict_types=1);

namespace App\Context\User\Application\CQRS\Command\Handler;

use App\Context\Common\Infrastructure\Contract\CommandHandlerInterface;
use App\Context\User\Application\CQRS\Command\UpdateUserCommand;
use App\Context\User\Domain\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class UpdateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface   $entityManager,
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $dto = $command->getDto();
        $user = $dto->getUser();

        foreach ($dto->getLocations() as $location) {
            $user->addLocation($location);
        }

        foreach ($dto->getContacts() as $contactDto) {
            $contact = new Contact($user, $contactDto->getCode(), $contactDto->getName(), $contactDto->getValue());
            $this->entityManager->persist($contact);
            $user->addContact($contact);
        }

        $events = $user->getEvents();
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
