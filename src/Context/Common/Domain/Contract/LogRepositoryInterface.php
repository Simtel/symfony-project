<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Contract;

use App\Context\Common\Domain\Entity\Log;

interface LogRepositoryInterface
{
    /**
     * @return Log[]
     */
    public function findAll(): array;
}
