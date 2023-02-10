<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Contract;

use App\Context\User\Domain\Entity\User;

interface UserServiceInterface
{
    public function calculateAccesses(User $user): void;
}
