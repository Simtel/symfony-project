<?php

declare(strict_types=1);

use App\Context\Common\Infrastructure\Contract\CommandHandlerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/context/common/');

    $containerConfigurator->import(__DIR__ . '/context/user/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->instanceof(CommandHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus'=> 'command.bus']);

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([
        __DIR__ . '/../src/DependencyInjection/',
        __DIR__ . '/../src/Entity/',
        __DIR__ . '/../src/Kernel.php',
        __DIR__ . '/../src/Context/Common/',
        __DIR__ . '/../src/Context/User/',
    ]);
};
