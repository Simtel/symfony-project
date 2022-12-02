<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\View;

use App\Context\Common\Domain\Entity\Config;

class ConfigView
{
    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function getName(): string
    {
        return $this->config->getName();
    }

    public function getValue(): ?string
    {
        return $this->config->getValue();
    }

    public function getUpdatedAt(): string
    {
        return $this->config->getUpdateAt()->format('Y-m-d H:i:s');
    }
}
