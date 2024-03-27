<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ObjectRepository;
use Exception;

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

    public function getRepository($className): EntityRepository|ObjectRepository
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

    public function transactional($func)
    {
        return $this->wrappedEntityManager->transactional($func);
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

    public function createNamedQuery($name): Query
    {
        return $this->wrappedEntityManager->createNamedQuery($name);
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm): NativeQuery
    {
        return $this->wrappedEntityManager->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name): NativeQuery
    {
        return $this->wrappedEntityManager->createNamedNativeQuery($name);
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->wrappedEntityManager->createQueryBuilder();
    }

    public function getReference($entityName, $id)
    {
        return $this->wrappedEntityManager->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        return $this->wrappedEntityManager->getPartialReference($entityName, $identifier);
    }

    public function close(): void
    {
        $this->wrappedEntityManager->close();
    }

    /** @noinspection ParameterDefaultValueIsNotNullInspection */
    public function copy($entity, $deep = false): object
    {
        return $this->wrappedEntityManager->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null): void
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

    public function getHydrator($hydrationMode): AbstractHydrator
    {
        return $this->wrappedEntityManager->getHydrator($hydrationMode);
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

    public function find($className, $id)
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

    public function refresh($object): void
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

    /**
     * @throws Exception
     */
    public function persistAndCommit(object $entity): void
    {
        $this->wrappedEntityManager->persist($entity);
        $this->wrappedEntityManager->getUnitOfWork()->commit($entity);
        $this->wrappedEntityManager->refresh($entity);

        $this->addEntityToTruncate($entity);
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

    /**
     * @return \Doctrine\Persistence\Mapping\ClassMetadataFactory<\Doctrine\Persistence\Mapping\ClassMetadata<object>>
     */
    public function getMetadataFactory(): \Doctrine\Persistence\Mapping\ClassMetadataFactory
    {
        /** @phpstan-ignore-next-line */
        return $this->wrappedEntityManager->getMetadataFactory();
    }
}
