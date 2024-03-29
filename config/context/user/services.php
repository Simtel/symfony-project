<?php

declare(strict_types=1);

use App\Context\Common\Infrastructure\Contract\CommandHandlerInterface;
use App\Context\User\Infrastructure\Service\UserService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('string $publicDir', '%kernel.project_dir%/public');

    $services->instanceof(CommandHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus'=> 'command.bus']);

    $services->load('App\Context\User\\', __DIR__ . '/../../../src/Context/User/');

    $services->set(UserService::class)
        ->public();
};
