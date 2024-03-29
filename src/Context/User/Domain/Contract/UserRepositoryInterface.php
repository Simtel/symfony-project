<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Contract;

use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @param array<string,mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return User|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?User;

    public function find(int $id): ?User;

    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @return User[]
     */
    public function findByLocation(Location $location): array;

    /**
     * @return User[]
     */
    public function findAllWithLocations(): array;
}
