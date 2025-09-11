<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\View;

final readonly class ConfigListView
{
    /**
     * @var ConfigView[]
     */
    private array $configs;

    /**
     * @param ConfigView[] $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return ConfigView[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }
}
