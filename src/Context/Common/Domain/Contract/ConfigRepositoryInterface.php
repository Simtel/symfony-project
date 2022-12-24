<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Contract;

use App\Context\Common\Domain\Entity\Config;

interface ConfigRepositoryInterface
{
    /**
     * @return Config[]
     */
    public function findAll(): array;

    public function getByName(string $name): Config;
}
