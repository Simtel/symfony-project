<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use ApiTestCase\JsonApiTestCase;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

abstract class FeatureTest extends JsonApiTestCase
{
    private ?User $currentUser = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }


    public function getJson(string $uri, array $parameters = [], array $headers = []): Response
    {
        $this->client->request(
            'GET',
            $uri,
            $parameters,
            [],
            $this->transformHeadersToServerVars(array_merge($this->getDefaultHeaders(), $headers))
        );

        return $this->client->getResponse();
    }


    /**
     * @throws JsonException
     */
    public function postJson(string $uri, array $data = [], array $headers = []): Response
    {
        $content = json_encode($data, JSON_THROW_ON_ERROR);

        $jsonHeaders = [
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ];

        $headers = array_replace($jsonHeaders, array_merge($this->getDefaultHeaders(), $headers));


        $this->client->request(
            'POST',
            $uri,
            [],
            [],
            $this->transformHeadersToServerVars($headers),
            $content
        );

        return $this->client->getResponse();
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

    protected function tearDown(): void
    {
        $this->currentUser = null;
        parent::tearDown();
    }

    /**
     * @throws ORMException
     */
    public function createUser(): User
    {
        $em = $this->getEntityManager();

        $user = new User('test@mail.com', 'Test', '456');
        $user->setToken('4444');
        $em->persist($user);

        $em->flush();

        return $user;
    }
}
