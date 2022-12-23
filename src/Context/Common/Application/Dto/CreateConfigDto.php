<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Dto;

readonly class CreateConfigDto
{
    public function __construct(
        private string $name,
        private ?string $value
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
