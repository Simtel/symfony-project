<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\Common;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use App\Context\Common\Application\Service\ConfigProvider;
use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use App\Context\Common\Infrastructure\View\ConfigView;
use App\Context\User\Domain\Entity\User;
use App\Tests\Feature\FeatureTestBaseCase;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

final class ConfigProviderTest extends FeatureTestBaseCase
{
    /**
     * @test
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testProviderCanReturnEmptyView(): void
    {
        $repositoryMock = $this->getMockBuilder(ConfigRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll','getByName'])
            ->getMock();

        $repositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        /** @var CacheItemPoolInterface $cachePool */
        $cachePool = self::getContainer()->get(CacheItemPoolInterface::class);

        $provider = new ConfigProvider($repositoryMock, $cachePool);

        $result = $provider->getList();

        self::assertSame([], $result->getConfigs());
    }


    /**
     * @test
     *
     * @throws Exception
     */
    public function testProviderReturnFillViewConfig(): void
    {
        $user = new User('test@test.com', 'Test', '4444', '3232323');
        $config = new Config('app', 'Test Project', $user);
        $this->getEntityManager()->persist($config);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        /** @var ConfigProviderInterface $provider */
        $provider = self::getContainer()->get(ConfigProviderInterface::class);

        $result = $provider->getList();

        self::assertSame(
            [
                [
                    'name' => $config->getName(),
                    'value' => $config->getValue(),
                    'updatedAt' => $config->getUpdateAt()->format('Y-m-d H:i:s'),
                ]
            ],
            array_map(
                static fn (ConfigView $configView) => [
                    'name' => $configView->getName(),
                    'value' => $configView->getValue(),
                    'updatedAt' => $configView->getUpdatedAt()
                ],
                $result->getConfigs()
            )
        );
    }
}
