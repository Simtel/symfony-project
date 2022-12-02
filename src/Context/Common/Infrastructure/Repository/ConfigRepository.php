<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Repository;

use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ConfigRepository implements ConfigRepositoryInterface
{
    private readonly EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->repository = new EntityRepository(
            $this->entityManager,
            $entityManager->getClassMetadata(Config::class),
        );
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
