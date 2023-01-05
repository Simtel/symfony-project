<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Contract;

use App\Context\Common\Domain\Entity\Log;

interface LogProviderInterface
{
    /**
     * @return Log[]
     */
    public function getList(): array;
}
