<?php

declare(strict_types=1);

use App\Context\User\Domain\Event\LocationAddedEvent;
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
                'user_events_transport' => [
                    'dsn' => '%env(RABBIT_DSN)%',
                    'options' => [
                        'exchange' => [
                            'name' => 'user.events',
                            'type' => 'topic',
                        ],
                        'queues' => [
                            'user.events' => [
                                'binding_keys' => [
                                    'user.events.add_location',
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'routing' => [
                LocationAddedEvent::class => 'user_events_transport'
            ],
        ],
    ]);
};
