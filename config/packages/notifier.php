<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'notifier' => [
            'chatter_transports' => [
                'telegram' => '%env(TELEGRAM_DSN)%',
            ],
            'channel_policy' => [
                'urgent' => [
                    'chat/telegram',
                ],
                'high' => [
                    'chat/telegram',
                ],
                'medium' => [
                    'chat/telegram',
                ],
                'low' => [
                    'chat/telegram',
                ],
            ],
        ],
    ]);
};
