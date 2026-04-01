<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('string $publicDir', '%kernel.project_dir%/public');

    $services->load('App\Context\User\\', dirname(__DIR__, 3) . '/src/Context/User/');
};
