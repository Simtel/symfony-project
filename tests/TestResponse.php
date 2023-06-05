<?php

declare(strict_types=1);

namespace App\Tests;

use JsonException;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;

class TestResponse
{
    private mixed $jsonCache = null;

    public function __construct(
        private readonly Response $baseResponse,
    ) {
    }

    public function getBaseResponse(): Response
    {
        return $this->baseResponse;
    }

    public function assertSee(string $value): TestResponse
    {
        $content = $this->baseResponse->getContent();
        $content = $content === false ? '' : $content;

        Assert::assertStringContainsString($value, $content);

        return $this;
    }

    public function assertDontSee(string $value): TestResponse
    {
        $content = $this->baseResponse->getContent();
        $content = $content === false ? '' : $content;

        Assert::assertStringNotContainsString($value, $content);

        return $this;
    }

    public function assertStatus(int $statusCode): TestResponse
    {
        $actualStatusCode = $this->baseResponse->getStatusCode();

        Assert::assertSame(
            $actualStatusCode,
            $statusCode,
            'Expected status code ' . $statusCode . ' but received ' . $actualStatusCode . '.',
        );

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function json(
        ?string $key = null,
        mixed $default = null,
    ): mixed {
        $content = $this->baseResponse->getContent();
        $content = $content === false ? '{}' : $content;

        if ($this->jsonCache === null) {
            $this->jsonCache = json_decode(
                $content,
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        }

        if ($key !== null) {
            return $this->jsonCache[$key] ?? $default;
        }

        return $this->jsonCache;
    }
}
