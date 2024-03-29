<?php

declare(strict_types=1);

use App\Context\Common\Infrastructure\EventListener\ApiExceptionEventListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\Context\Common\\', __DIR__ . '/../../../src/Context/Common/');

    $services->set(ApiExceptionEventListener::class)
        ->tag('kernel.event_listener', [
        'event' => 'kernel.exception',
    ]);
};
