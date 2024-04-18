<?php

namespace App\Tests\Feature;

use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use JsonException as JsonExceptionAlias;

use function PHPUnit\Framework\assertSame;

class ConfigTest extends FeatureTestBaseCase
{
    /**
     * @throws Exception
     */
    public function testAccessDenied(): void
    {
        $response = $this->getJson('/api/config/list');
        $response->assertStatus(403);
    }

    /**
     * @throws Exception
     */
    public function testAllowAccess(): void
    {
        $em = $this->getEntityManager();

        $user = new User('test@mail.com', 'Test', '456');
        $user->setToken('4444');
        $em->persist($user);
        $em->flush();

        $this->loginAs($user);

        $response = $this->getJson('/api/config/list');

        $response->assertStatus(200);
        self::assertSame([], $response->json('configs'));
    }

    /**
     * @throws Exception
     */
    public function testGetConfigs(): void
    {
        $em = $this->getEntityManager();

        $user = new User('test@mail.com', 'Test', '456');
        $user->setToken('123');
        $em->persist($user);

        $config = new Config('app', 'Test App', $user);
        $em->persist($config);

        $em->flush();

        $this->loginAs($user);

        $response = $this->getJson('/api/config/list');

        $response->assertStatus(200);
        assertSame(
            [
                [
                    'uuid' => $config->getId()->toRfc4122(),
                    "name" => "app",
                    "value" => "Test App",
                    "updatedAt" => $config->getUpdateAt()->format('Y-m-d H:i:s'),
                    "user" => [
                        "name" => "Test",
                        "id" => $user->getId()
                    ]
                ]

            ],
            $response->json('configs')
        );
    }

    /**
     * @throws ORMException
     * @throws JsonExceptionAlias
     * @throws Exception
     */
    public function testValidateRequiredConfigName(): void
    {
        $this->loginAs($this->createUser());

        $response = $this->postJson('/api/config', ['value' => '13']);

        $response->assertStatus(422);
        self::assertSame(
            ['name: This field is missing.'],
            $response->json('errors'),
        );

    }

    /**
     * @throws ORMException
     * @throws JsonExceptionAlias
     * @throws Exception
     */
    public function testValidateRequiredConfigValue(): void
    {
        $this->loginAs($this->createUser());

        $response = $this->postJson('/api/config', ['name' => 'config_name']);

        $response->assertStatus(422);
        self::assertSame(
            ['value: This field is missing.'],
            $response->json('errors'),
        );
    }

    /**
     * @throws ORMException
     * @throws JsonExceptionAlias
     * @throws Exception
     */
    public function testCreateConfig(): void
    {
        $user = $this->createUser();
        $this->loginAs($user);

        $response = $this->postJson('/api/config', ['name' => 'app', 'value' => 'name']);

        /** @var ConfigRepositoryInterface $repository */
        $repository = self::getContainer()->get(ConfigRepositoryInterface::class);
        $config = $repository->getByName('app');

        $response->assertStatus(201);
        self::assertSame('app', $config->getName());
        self::assertSame('name', $config->getValue());
        self::assertSame($user->getId(), $config->getCreatedBy()->getId());
    }
}
