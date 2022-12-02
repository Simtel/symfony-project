<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Service;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Infrastructure\View\ConfigListView;
use App\Context\Common\Infrastructure\View\ConfigView;

class ConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly ConfigRepositoryInterface $configRepository
    ) {
    }

    public function getList(): ConfigListView
    {
        $configs = [];
        foreach ($this->configRepository->findAll() as $config) {
            $configs[] = new ConfigView($config);
        }

        return new ConfigListView($configs);
    }
}
