<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import([
        'path' => '../../src/Context/User/Infrastructure/Controller/',
        'namespace' => 'App\Context\User\Infrastructure\Controller',
    ], 'attribute');
};
