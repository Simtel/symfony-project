<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Service;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Infrastructure\View\ConfigListView;
use App\Context\Common\Infrastructure\View\ConfigView;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

final readonly class ConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private ConfigRepositoryInterface $configRepository,
        private CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getList(): ConfigListView
    {
        $cacheConfigs = $this->cache->getItem('list.configs');
        if (!$cacheConfigs->isHit()) {
            $configs = [];
            $cacheConfigs->expiresAfter(3600);
            foreach ($this->configRepository->findAll() as $config) {
                $configs[] = new ConfigView($config);
            }
            $cacheConfigs->set($configs);
        }
        /** @var ConfigView[] $listConfig */
        $listConfig = $cacheConfigs->get();
        return new ConfigListView($listConfig);
    }
}
