<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\View;

use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Infrastructure\View\UserView;

readonly class ConfigView
{
    public function __construct(
        private Config $config,
    ) {
    }

    public function getUuid(): string
    {
        return $this->config->getId()->toRfc4122();
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

    public function getUser(): UserView
    {
        return new UserView($this->config->getCreatedBy());
    }
}
