<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Repository;

use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * @var EntityRepository<Config>
     */
    private EntityRepository $repository;

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

    /**
     * @throws EntityNotFoundException
     */
    public function getByName(string $name): Config
    {
        $config = $this->repository->findOneBy(['name' => $name]);
        if ($config === null) {
            throw new EntityNotFoundException($name . ' config not found');
        }

        return $config;
    }
}
