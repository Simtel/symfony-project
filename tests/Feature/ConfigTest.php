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

        $user = new User('test@mail.com', 'Test', '456', '4444');
        $em->persist($user);
        $em->flush();

        $this->loginAs($user);

        $response = $this->getJson('/api/config/list');

        $response->assertStatus(200);
        self::assertSame(['configs' => []], $response->json());
    }

    /**
     * @throws Exception
     */
    public function testGetConfigs(): void
    {
        $em = $this->getEntityManager();

        $user = new User('test@mail.com', 'Test', '456', '123');
        $em->persist($user);

        $config = new Config('app', 'Test App', $user);
        $em->persist($config);

        $em->flush();

        $this->loginAs($user);

        $response = $this->getJson('/api/config/list');

        $response->assertStatus(200);
        assertSame(
            [
                'configs' => [
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
                ]
            ],
            $response->json()
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

        // Testing with empty name (our custom validation)
        $response = $this->postJson('/api/config', ['name' => '', 'value' => '13']);

        // The controller should return 422 for validation error
        $response->assertStatus(422);

        // Get response and check structure exists
        $responseData = $response->json();
        self::assertIsArray($responseData, 'Response should be an array');

        // Validate error response structure
        self::assertArrayHasKey('error', $responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertArrayHasKey('details', $responseData);

        self::assertTrue($responseData['error']);
        self::assertEquals('Ошибка валидации данных', $responseData['message']);

        // Ensure details is an array and has violations
        self::assertIsArray($responseData['details'], 'Details should be an array');
        self::assertArrayHasKey('violations', $responseData['details']);
        self::assertIsArray($responseData['details']['violations'], 'Violations should be an array');
        self::assertArrayHasKey('name', $responseData['details']['violations']);
        self::assertEquals('Имя конфигурации не может быть пустым', $responseData['details']['violations']['name']);
    }

    /**
     * @throws ORMException
     * @throws JsonExceptionAlias
     * @throws Exception
     */
    public function testValidateRequiredConfigValue(): void
    {
        $this->loginAs($this->createUser());

        // Testing with empty value (our custom validation)
        $response = $this->postJson('/api/config', ['name' => 'config_name', 'value' => '']);

        // The controller should return 422 for validation error
        $response->assertStatus(422);

        // Get response and check structure exists
        $responseData = $response->json();
        self::assertIsArray($responseData, 'Response should be an array');

        // Validate error response structure
        self::assertArrayHasKey('error', $responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertArrayHasKey('details', $responseData);

        self::assertTrue($responseData['error']);
        self::assertEquals('Ошибка валидации данных', $responseData['message']);

        // Ensure details is an array and has violations
        self::assertIsArray($responseData['details'], 'Details should be an array');
        self::assertArrayHasKey('violations', $responseData['details']);
        self::assertIsArray($responseData['details']['violations'], 'Violations should be an array');
        self::assertArrayHasKey('value', $responseData['details']['violations']);
        self::assertEquals('Значение конфигурации не может быть пустым', $responseData['details']['violations']['value']);
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
