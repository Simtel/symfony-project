<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Service;

use App\Context\User\Domain\Contract\UserServiceInterface;
use App\Context\User\Domain\Entity\User;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class UserService implements UserServiceInterface
{
    public function __construct(private LockFactory $lockFactory)
    {
    }

    public function calculateAccesses(User $user): void
    {
        $lock = $this->lockFactory->createLock('calculateUserAccesses-' . $user->getId());
        if (!$lock->acquire()) {
            throw new AccessDeniedException('User service is locked');
        }
        if ($lock->acquire(true)) {
            sleep(3);
            $lock->release();
        }
    }
}
