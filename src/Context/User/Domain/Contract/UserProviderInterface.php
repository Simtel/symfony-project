<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Contract;

use App\Context\User\Domain\Entity\User;

interface UserProviderInterface
{
    public function getCurrentUser(): User;
}
