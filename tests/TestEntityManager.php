<?php

declare(strict_types=1);

namespace App\Tests;

use DateTimeInterface;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

/**
 * @method bool isUninitializedObject(mixed $value)
 */
class TestEntityManager implements EntityManagerInterface
{
    /**
     * @var class-string[]
     */
    private array $entityClassesToTruncate = [];

    /**
     * @var array<class-string, bool>
     */
    private array $forbiddenToTruncateEntityClasses = [];

    public function __construct(
        private readonly EntityManagerInterface $wrappedEntityManager,
    ) {
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function getRepository($className): EntityRepository
    {
        return $this->wrappedEntityManager->getRepository($className);
    }

    public function getCache(): ?Cache
    {
        return $this->wrappedEntityManager->getCache();
    }

    public function getConnection(): Connection
    {
        return $this->wrappedEntityManager->getConnection();
    }

    public function getExpressionBuilder(): Expr
    {
        return $this->wrappedEntityManager->getExpressionBuilder();
    }

    public function beginTransaction(): void
    {
        $this->wrappedEntityManager->beginTransaction();
    }



    public function commit(): void
    {
        $this->wrappedEntityManager->commit();
    }

    public function rollback(): void
    {
        $this->wrappedEntityManager->rollback();
    }

    /** @noinspection ParameterDefaultValueIsNotNullInspection */
    public function createQuery($dql = ''): Query
    {
        return $this->wrappedEntityManager->createQuery($dql);
    }


    public function createNativeQuery(string $sql, ResultSetMapping $rsm): NativeQuery
    {
        return $this->wrappedEntityManager->createNativeQuery($sql, $rsm);
    }


    public function createQueryBuilder(): QueryBuilder
    {
        return $this->wrappedEntityManager->createQueryBuilder();
    }

    public function getReference($entityName, $id): ?object
    {
        return $this->wrappedEntityManager->getReference($entityName, $id);
    }


    public function close(): void
    {
        $this->wrappedEntityManager->close();
    }


    public function lock(object $entity, LockMode|int $lockMode, DateTimeInterface|int|null $lockVersion = null): void
    {
        $this->wrappedEntityManager->lock($entity, $lockMode, $lockVersion);
    }

    public function getEventManager(): EventManager
    {
        return $this->wrappedEntityManager->getEventManager();
    }

    public function getConfiguration(): Configuration
    {
        return $this->wrappedEntityManager->getConfiguration();
    }

    public function isOpen(): bool
    {
        return $this->wrappedEntityManager->isOpen();
    }

    public function getUnitOfWork(): UnitOfWork
    {
        return $this->wrappedEntityManager->getUnitOfWork();
    }


    public function newHydrator($hydrationMode): AbstractHydrator
    {
        return $this->wrappedEntityManager->newHydrator($hydrationMode);
    }

    public function getProxyFactory(): ProxyFactory
    {
        return $this->wrappedEntityManager->getProxyFactory();
    }

    public function getFilters(): FilterCollection
    {
        return $this->wrappedEntityManager->getFilters();
    }

    public function isFiltersStateClean(): bool
    {
        return $this->wrappedEntityManager->isFiltersStateClean();
    }

    public function hasFilters(): bool
    {
        return $this->wrappedEntityManager->hasFilters();
    }

    public function getClassMetadata($className): ClassMetadata
    {
        return $this->wrappedEntityManager->getClassMetadata($className);
    }

    public function find($className, $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null): ?object
    {
        return $this->wrappedEntityManager->find($className, $id);
    }

    public function persist($object): void
    {
        $this->wrappedEntityManager->persist($object);
        $this->addEntityToTruncate($object);
    }

    public function remove($object): void
    {
        $this->wrappedEntityManager->remove($object);
    }

    public function clear(): void
    {
        $this->wrappedEntityManager->clear();
    }

    public function detach($object): void
    {
        $this->wrappedEntityManager->detach($object);
    }

    public function refresh($object, LockMode|int|null $lockMode = null): void
    {
        $this->wrappedEntityManager->refresh($object);
    }

    public function flush(): void
    {
        $this->wrappedEntityManager->flush();
    }

    public function initializeObject(object $obj): void
    {
        $this->wrappedEntityManager->initializeObject($obj);
    }

    public function contains(object $object): bool
    {
        return $this->wrappedEntityManager->contains($object);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function truncateEntityTables(): void
    {
        if (count($this->entityClassesToTruncate) <= 0) {
            return;
        }
        $connection = $this->wrappedEntityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->entityClassesToTruncate as $entityClass) {
            $table = $this->getClassMetadata($entityClass)->getTableName();

            $query = $databasePlatform->getTruncateTableSQL($table);

            $connection->executeQuery($query);
        }
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');

        $this->entityClassesToTruncate = [];
    }



    private function addEntityToTruncate(object $entity): void
    {
        $className = $entity::class;
        if (
            array_key_exists($className, $this->forbiddenToTruncateEntityClasses)
            || in_array($className, $this->entityClassesToTruncate, true)
        ) {
            return;
        }
        $this->entityClassesToTruncate[] = $className;
    }

    /** @phpstan-ignore-next-line  */
    public function getMetadataFactory(): ClassMetadataFactory
    {
        return $this->wrappedEntityManager->getMetadataFactory();
    }

    public function wrapInTransaction(callable $func): mixed
    {
        return $this->wrappedEntityManager->wrapInTransaction($func);
    }
}
