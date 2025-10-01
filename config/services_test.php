<?php

declare(strict_types=1);

use App\Tests\TestEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\DataCollector\MessageDataCollector;
use Symfony\Component\Mailer\EventListener\MessageLoggerListener;

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

    $services
        ->set('mailer.message_logger_listener', MessageLoggerListener::class)
        ->args([
            service('profiler.is_disabled_state_checker')->nullOnInvalid(),
        ])
        ->tag('kernel.event_subscriber')
        ->tag('kernel.reset', ['method' => 'reset'])

        ->set('mailer.data_collector', MessageDataCollector::class)
        ->args([
            service('mailer.message_logger_listener'),
        ])
        ->tag('data_collector', [
            'template' => '@WebProfiler/Collector/mailer.html.twig',
            'id' => 'mailer',
        ])
    ;
};
