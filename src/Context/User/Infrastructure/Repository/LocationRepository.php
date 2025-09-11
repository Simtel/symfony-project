<?php

namespace App\Context\User\Infrastructure\Repository;

use App\Context\User\Domain\Contract\LocationRepositoryInterface;
use App\Context\User\Domain\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var EntityRepository<Location>
     */
    private EntityRepository $repository;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->repository = new EntityRepository(
            $entityManager,
            $entityManager->getClassMetadata(Location::class)
        );
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?Location
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    public function find(int $id): ?Location
    {
        return $this->repository->find($id);
    }
}
