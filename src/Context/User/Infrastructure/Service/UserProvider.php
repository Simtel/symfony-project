<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Service;

use App\Context\User\Domain\Contract\UserProviderInterface;
use App\Context\User\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

readonly class UserProvider implements UserProviderInterface
{
    public function __construct(private Security $security)
    {
    }

    public function getCurrentUser(): User
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException('No current user found!');
        }

        return $user;
    }
}
