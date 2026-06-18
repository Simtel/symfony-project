<?php

declare(strict_types=1);

use App\Context\Common\Infrastructure\Security\ApiKeyAuthenticator;
use App\Context\User\Domain\Entity\User;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'app_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'token',
                ],
            ],
        ],
        'firewalls' => [
            'main' => [
                'lazy' => true,
                'stateless' => true,
                'provider' => 'app_user_provider',
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
