<?php

declare(strict_types=1);

use App\Context\Common\Infrastructure\Command\InMemoryCommandBus;
use App\Context\Common\Infrastructure\Contract\CommandBusInterface;
use App\Context\Common\Infrastructure\Contract\CommandHandlerInterface;
use App\Context\Common\Infrastructure\EventListener\ApiExceptionEventListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->instanceof(CommandHandlerInterface::class)
        ->tag('internal.command_handler');

    $services->set(CommandBusInterface::class, InMemoryCommandBus::class)
        ->args([\Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator('internal.command_handler')]);

    $services->load('App\Context\Common\\', __DIR__ . '/../../../src/Context/Common/');

    $services->set(ApiExceptionEventListener::class)
        ->tag('kernel.event_listener', [
        'event' => 'kernel.exception',
    ]);
};
