<?php

namespace App\Tests\Feature;

use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use JsonException as JsonExceptionAlias;

class ConfigTest extends FeatureTestBaseCase
{
    public function testAccessDenied(): void
    {
        $this->getJson('/api/config/list');
        self::assertResponseStatusCodeSame(403);
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

        self::assertSame(200, $response->getStatusCode());
        $this->assertResponse($response, 'Common/configs_empty');
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

        self::assertSame(200, $response->getStatusCode());
        $this->assertResponse($response, 'Common/configs');
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

        self::assertSame(422, $response->getStatusCode());
        $this->assertJsonResponseContent($response, 'Common/configs_validate_missing_name');
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

        self::assertSame(422, $response->getStatusCode());
        $this->assertJsonResponseContent($response, 'Common/configs_validate_missing_value');
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

        $this->postJson('/api/config', ['name' => 'app', 'value' => 'name']);

        /** @var ConfigRepositoryInterface $repository */
        $repository = self::getContainer()->get(ConfigRepositoryInterface::class);
        $config = $repository->getByName('app');

        self::assertResponseStatusCodeSame(201);
        self::assertSame('app', $config->getName());
        self::assertSame('name', $config->getValue());
        self::assertSame($user->getId(), $config->getCreatedBy()->getId());
    }
}
