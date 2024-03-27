<?php

declare(strict_types=1);

use App\Context\Common\Infrastructure\Security\ApiKeyAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'users_in_memory' => [
                'memory' => null,
            ],
        ],
        'firewalls' => [
            'main' => [
                'lazy' => true,
                'logout' => true,
                'stateless' => true,
                'custom_authenticators' => [
                    ApiKeyAuthenticator::class,
                ],
            ],
        ],
        'access_control' => [
            [
                'path' => '^/api',
                'roles' => 'ROLE_USER',
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
