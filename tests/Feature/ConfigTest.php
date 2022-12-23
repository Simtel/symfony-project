<?php

namespace App\Tests\Feature;

use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use JsonException as JsonExceptionAlias;

class ConfigTest extends FeatureTest
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
}
