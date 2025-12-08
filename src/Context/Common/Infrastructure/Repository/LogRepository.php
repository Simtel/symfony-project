<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Repository;

use App\Context\Common\Domain\Contract\LogRepositoryInterface;
use App\Context\Common\Domain\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class LogRepository implements LogRepositoryInterface
{
    /**
     * @var EntityRepository<Log>
     */
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->repository = new EntityRepository(
            $this->entityManager,
            $entityManager->getClassMetadata(Log::class),
        );
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
