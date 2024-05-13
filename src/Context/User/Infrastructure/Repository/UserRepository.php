<?php

namespace App\Context\User\Infrastructure\Repository;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var EntityRepository<User>
     */
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->repository = new EntityRepository(
            $this->entityManager,
            $this->entityManager->getClassMetadata(User::class)
        );
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?User
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    public function find(int $id): ?User
    {
        return $this->repository->find($id);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @return User[]
     */
    public function findByLocation(Location $location): array
    {
        $builder = $this->repository->createQueryBuilder('users');
        $builder->innerJoin('users.locations', 'locations')
            ->where('locations IN (:location)')
            ->setParameter('location', $location);

        return $builder->getQuery()->getResult();
    }

    public function findAllWithLocations(): array
    {
        $builder = $this->repository->createQueryBuilder('users')
            ->addSelect('locations')
            ->leftJoin('users.locations', 'locations');

        return $builder->getQuery()->getResult();
    }

    /**
     * @param  int[]  $ids
     * @return User[]
     */
    public function findByIds(array $ids): array
    {
        return $this->repository->findBy(['id' => $ids]);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }
}
