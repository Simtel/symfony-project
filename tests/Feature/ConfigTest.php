<?php

namespace App\Tests\Feature;

use App\Context\Common\Domain\Contract\ConfigRepositoryInterface;
use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use JsonException as JsonExceptionAlias;

use function PHPUnit\Framework\assertSame;

final class ConfigTest extends FeatureTestBaseCase
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

    /**
     * @throws ORMException
     * @throws JsonExceptionAlias
     * @throws Exception
     */
    public function testDeleteConfig(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();
        $this->loginAs($user);

        // Создаем конфигурацию для удаления
        $config = new Config('test_config', 'test_value', $user);
        $em->persist($config);
        $em->flush();

        $configId = $config->getId()->toString();

        // Удаляем конфигурацию
        $response = $this->deleteJson('/api/config/' . $configId);

        $response->assertStatus(200);
        $responseData = $response->json();
        self::assertIsArray($responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('Конфигурация успешно удалена', $responseData['message']);

        // Проверяем, что конфигурация действительно удалена
        $this->expectException(\Throwable::class);
        $repository = self::getContainer()->get(ConfigRepositoryInterface::class);
        $repository->findById($config->getId());
    }

    /**
     * @throws Exception
     */
    public function testDeleteNonExistentConfig(): void
    {
        $user = $this->createUser();
        $this->loginAs($user);

        // Пытаемся удалить несуществующую конфигурацию
        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $response = $this->deleteJson('/api/config/' . $nonExistentId);

        $response->assertStatus(404);
        $responseData = $response->json();
        self::assertIsArray($responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertSame('Конфигурация с указанным ID не найдена', $responseData['message']);
    }

    /**
     * @throws Exception
     */
    public function testDeleteConfigWithInvalidUuid(): void
    {
        $user = $this->createUser();
        $this->loginAs($user);

        // Пытаемся удалить с некорректным UUID
        $invalidId = 'invalid-uuid-format';
        $response = $this->deleteJson('/api/config/' . $invalidId);

        $response->assertStatus(422);
        $responseData = $response->json();
        self::assertIsArray($responseData);
        self::assertArrayHasKey('error', $responseData);
        self::assertArrayHasKey('message', $responseData);
        self::assertArrayHasKey('details', $responseData);
        self::assertSame('Ошибка валидации данных', $responseData['message']);
        self::assertArrayHasKey('violations', $responseData['details']);
        self::assertArrayHasKey('id', $responseData['details']['violations']);
        self::assertSame('Некорректный формат ID конфигурации', $responseData['details']['violations']['id']);
    }

    /**
     * @throws Exception
     */
    public function testDeleteConfigWithoutAccess(): void
    {
        // Удаляем конфигурацию без авторизации
        $response = $this->deleteJson('/api/config/123');

        $response->assertStatus(403);
    }

}
