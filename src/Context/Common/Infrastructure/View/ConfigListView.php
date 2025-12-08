<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\View;

final readonly class ConfigListView
{
    /**
     * @param ConfigView[] $configs
     */
    public function __construct(private array $configs)
    {
    }

    /**
     * @return ConfigView[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }
}
