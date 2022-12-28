<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Contract;

use App\Context\Common\Domain\Contract\CommandInterface;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;
}
