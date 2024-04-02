<?php

declare(strict_types=1);

namespace App\Context\User\Application\CQRS\Command;

use App\Context\Common\Domain\Contract\CommandInterface;
use App\Context\User\Application\CQRS\Command\Handler\UpdateUserCommandHandler;
use App\Context\User\Application\Dto\UpdateUserDto;

/**
 * @see UpdateUserCommandHandler
 */
readonly class UpdateUserCommand implements CommandInterface
{
    public function __construct(private UpdateUserDto $dto)
    {
    }

    public function getDto(): UpdateUserDto
    {
        return $this->dto;
    }
}
