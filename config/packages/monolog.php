<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'channels' => [
            'command',
        ],
    ]);
    if ($containerConfigurator->env() === 'dev') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                    'channels' => [
                        '!event',
                        '!command',
                    ],
                ],
                'console' => [
                    'type' => 'console',
                    'process_psr_3_messages' => false,
                    'path' => '%kernel.logs_dir%/console.log',
                    'channels' => [
                        'command',
                    ],
                ],
                'command' => [
                    'level' => 'debug',
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/command.log',
                    'channels' => [
                        'command',
                    ],
                ],
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'channels' => [
                        '!event',
                    ],
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                ],
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'buffer_size' => 50,
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => 'php://stderr',
                    'level' => 'debug',
                    'formatter' => 'monolog.formatter.json',
                ],
                'console' => [
                    'type' => 'console',
                    'process_psr_3_messages' => false,
                    'channels' => [
                        '!event',
                        '!doctrine',
                    ],
                ],
                'deprecation' => [
                    'type' => 'stream',
                    'channels' => [
                        'deprecation',
                    ],
                    'path' => 'php://stderr',
                ],
            ],
        ]);
    }
};
