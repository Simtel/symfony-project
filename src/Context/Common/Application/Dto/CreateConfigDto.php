<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Dto;

class CreateConfigDto
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $value
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
