<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\View;

use App\Context\User\Domain\Entity\User;

final readonly class UserView
{
    public function __construct(private User $user)
    {
    }

    public function getName(): string
    {
        return $this->user->getName();
    }

    public function getId(): int
    {
        return $this->user->getId();
    }
}
