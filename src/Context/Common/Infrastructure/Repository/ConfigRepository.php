<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Repository;

use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Uid\Uuid;

final readonly class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * @var EntityRepository<Config>
     */
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
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

    /**
     * @throws EntityNotFoundException
     */
    public function findById(Uuid $id): Config
    {
        $config = $this->repository->find($id);
        if ($config === null) {
            throw new EntityNotFoundException('Config with id ' . $id->toString() . ' not found');
        }

        return $config;
    }

    public function delete(Config $config): void
    {
        $this->entityManager->remove($config);
        $this->entityManager->flush();
    }
}
