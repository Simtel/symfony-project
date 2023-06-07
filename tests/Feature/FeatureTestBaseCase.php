<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Context\User\Domain\Entity\User;
use App\Tests\TestEntityManager;
use App\Tests\TestResponse;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class FeatureTestBaseCase extends KernelTestCase
{
    private ?User $currentUser = null;
    private ?TestEntityManager $entityManager = null;

    /**
     * @throws ConnectionException
     * @throws Exception
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        static::bootKernel(static::getKernelConfig());

        $entityManager = $this->getEntityManager();

        $connection = $entityManager->getConnection();

        $connection->setNestTransactionsWithSavepoints(true);
        $connection->beginTransaction();

    }

    protected static function getKernelConfig(): array
    {
        return [
            'environment' => 'test',
            'debug' => true,
        ];
    }

    protected function tearDown(): void
    {
        $this->currentUser = null;

        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $t= $connection->isTransactionActive();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        $entityManager->clear();
        $entityManager->close();

        $this->entityManager->truncateEntityTables();
        $this->entityManager = null;

        parent::tearDown();
    }

    /**
     * @throws \Exception
     */
    public function getJson(string $uri, array $parameters = [], array $headers = []): TestResponse
    {
        return $this->apiCall(
            'GET',
            $uri,
            $parameters,
            [],
            [],
            $this->transformHeadersToServerVars(array_merge($this->getDefaultHeaders(), $headers))
        );

    }

    /**
     * @throws \Exception
     */
    protected function apiCall(
        string  $method,
        string  $uri,
        array   $parameters = [],
        array   $cookies = [],
        array   $files = [],
        array   $server = [],
        ?string $content = null,
    ): TestResponse {
        $request = Request::create(
            $uri,
            $method,
            $parameters,
            $cookies,
            $files,
            $server,
            $content,
        );

        $response = self::$kernel->handle(
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            true,
        );

        self::$kernel->terminate($request, $response);

        return new TestResponse($response);
    }

    /**
     * @throws JsonException
     */
    public function postJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $content = json_encode($data, JSON_THROW_ON_ERROR);

        $jsonHeaders = [
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ];

        $headers = array_replace($jsonHeaders, array_merge($this->getDefaultHeaders(), $headers));

        return $this->apiCall(
            'POST',
            $uri,
            [],
            [],
            [],
            $this->transformHeadersToServerVars($headers),
            $content
        );

    }

    /**
     * @throws JsonException
     */
    public function putJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $content = json_encode($data, JSON_THROW_ON_ERROR);

        $jsonHeaders = [
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ];

        $headers = array_replace($jsonHeaders, array_merge($this->getDefaultHeaders(), $headers));


        return $this->apiCall(
            'PUT',
            $uri,
            [],
            [],
            [],
            $this->transformHeadersToServerVars($headers),
            $content
        );
    }

    public function loginAs(User $user): void
    {
        $this->currentUser = $user;
    }

    private function getDefaultHeaders(): array
    {
        return [
            'X-AUTH-TOKEN' => $this->currentUser?->getToken()
        ];
    }

    protected function transformHeadersToServerVars(array $headers): array
    {
        $transformed = [];
        foreach ($headers as $key => $value) {
            $name = str_replace('-', '_', strtoupper($key));
            $transformed[$this->formatServerHeaderKey($name)] = $value;
        }
        return $transformed;
    }

    protected function formatServerHeaderKey(string $name): string
    {
        if ($name !== 'CONTENT_TYPE' && $name !== 'REMOTE_ADDR' && !str_starts_with($name, 'HTTP_')) {
            return 'HTTP_' . $name;
        }
        return $name;
    }

    /**
     * @throws ORMException
     * @throws \Exception
     */
    public function createUser(array $overrides = []): User
    {
        $default = ['email' => 'test@mail22.com', 'name' => 'Test', 'password' => '456', 'token' => '4444'];
        $attributes = array_replace($default, $overrides);

        $em = $this->getEntityManager();

        $user = new User($attributes['email'], $attributes['name'], $attributes['password']);
        $user->setToken($attributes['token']);
        $em->persist($user);

        $em->flush();

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function getEntityManager(): TestEntityManager
    {
        if ($this->entityManager === null) {
            $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        }
        return $this->entityManager;
    }
}
