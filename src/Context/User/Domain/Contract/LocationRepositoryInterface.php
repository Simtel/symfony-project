<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Contract;

use App\Context\User\Domain\Entity\Location;

interface LocationRepositoryInterface
{
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Location;

    public function find(int $id): ?Location;
}
