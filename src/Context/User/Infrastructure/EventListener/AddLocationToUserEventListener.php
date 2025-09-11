<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\EventListener;

use App\Context\Common\Domain\Entity\Log;
use App\Context\User\Domain\Contract\UserProviderInterface;
use App\Context\User\Domain\Event\AddLocationToUserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class AddLocationToUserEventListener
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserProviderInterface $userProvider,
    ) {
    }

    public function __invoke(AddLocationToUserEvent $event): void
    {
        $action = $event->getLocation()->getName() . ' has been added to ' . $event->getUser()->getName();
        $log = new Log($action, $this->userProvider->getCurrentUser());

        $this->entityManager->persist($log);
    }
}
