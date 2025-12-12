<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Contract;

use App\Context\Common\Domain\Entity\Config;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Uid\Uuid;

interface ConfigRepositoryInterface
{
    /**
     * @return Config[]
     */
    public function findAll(): array;

    public function getByName(string $name): Config;

    /**
     * @throws EntityNotFoundException
     */
    public function findById(Uuid $id): Config;

    public function delete(Config $config): void;
}
