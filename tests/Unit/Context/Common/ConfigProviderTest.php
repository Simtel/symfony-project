<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\Common;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use App\Context\Common\Application\Service\ConfigProvider;
use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use App\Context\Common\Infrastructure\View\ConfigView;
use Doctrine\ORM\EntityManagerInterface;

use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigProviderTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    /**
     * @test
     *
     * @return void
     */
    public function providerCanReturnEmptyView(): void
    {
        $repositoryMock = $this->getMockBuilder(ConfigRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll'])
            ->getMock();

        $repositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        $provider = new ConfigProvider($repositoryMock);

        $result = $provider->getList();

        self::assertSame([], $result->getConfigs());
    }


    /**
     * @test
     *
     * @throws Exception
     */
    public function providerReturnFillViewConfig(): void
    {
        $config = new Config('app', 'Test Project');
        $this->entityManager->persist($config);
        $this->entityManager->flush();

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
                static function (ConfigView $configView) {
                    return [
                        'name' => $configView->getName(),
                        'value' => $configView->getValue(),
                        'updatedAt' => $configView->getUpdatedAt()
                    ];
                },
                $result->getConfigs()
            )
        );
    }
}