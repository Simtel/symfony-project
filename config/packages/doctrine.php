<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
        ],
        'orm' => [
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'report_fields_where_declared' => true,
            'controller_resolver' => [
                'auto_mapping' => false,
            ],
            'enable_native_lazy_objects' => true,
            'mappings' => [
                'Common' => [
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Context/Common/Domain/Entity/',
                    'prefix' => 'App\Context\Common\Domain\Entity',
                    'alias' => 'App',
                ],
                'User' => [
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Context/User/Domain/Entity/',
                    'prefix' => 'App\Context\User\Domain\Entity',
                    'alias' => 'App',
                ],
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
};
