<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Contract;

use App\Context\User\Domain\Entity\Location;

interface LocationRepositoryInterface
{
    /**
     * @param array<string,mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return Location|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Location;

    public function find(int $id): ?Location;
}
