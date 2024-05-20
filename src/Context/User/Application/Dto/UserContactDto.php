<?php

namespace App\Context\User\Application\Dto;

final readonly class UserContactDto
{
    public function __construct(
        private string  $code,
        private string  $name,
        private ?string $value,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
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
