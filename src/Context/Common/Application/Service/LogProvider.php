<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Service;

use App\Context\Common\Application\Contract\LogProviderInterface;
use App\Context\Common\Domain\Contract\LogRepositoryInterface;

readonly class LogProvider implements LogProviderInterface
{
    public function __construct(private LogRepositoryInterface $logRepository)
    {
    }

    public function getList(): array
    {
        return $this->logRepository->findAll();
    }
}
