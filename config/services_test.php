<?php

declare(strict_types=1);

use App\Tests\TestEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/context/common/');

    $containerConfigurator->import(__DIR__ . '/context/user/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([
        __DIR__ . '/../src/DependencyInjection/',
        __DIR__ . '/../src/Entity/',
        __DIR__ . '/../src/Kernel.php',
        __DIR__ . '/../src/Context/Common/',
        __DIR__ . '/../src/Context/User/',
    ]);

    $services->set('test_entity_manager', TestEntityManager::class)
        ->share(false)
        ->args([
        service('doctrine.orm.entity_manager'),
    ]);

    $services->alias(EntityManagerInterface::class, 'test_entity_manager');
};
