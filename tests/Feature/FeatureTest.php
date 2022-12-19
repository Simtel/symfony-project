<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use ApiTestCase\JsonApiTestCase;
use App\Context\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;

abstract class FeatureTest extends JsonApiTestCase
{
    private ?User $currentUser = null;

    protected function setUp(): void
    {
        parent::setUp();
       // $this->setUpDatabase();
    }


    public function json($uri, array $parameters = [], array $headers = []): Response
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
}
