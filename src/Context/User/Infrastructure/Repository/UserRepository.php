<?php

namespace App\Context\User\Infrastructure\Repository;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
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
        EntityManagerInterface $entityManager
    ) {
        $this->repository = new EntityRepository(
            $entityManager,
            $entityManager->getClassMetadata(User::class)
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
}
