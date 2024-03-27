<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'default_bus' => 'command.bus',
            'buses' => [
                'command.bus' => [
                    'middleware' => [
                        'doctrine_transaction',
                    ],
                ],
            ],
            'transports' => [
                'sync' => 'sync://',
            ],
            'routing' => null,
        ],
    ]);
};
