<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Doctrine\ORM\Exception\ORMException;
use JsonException;

class CommonTest extends FeatureTestBaseCase
{
    /**
     * @throws JsonException
     * @throws ORMException
     */
    public function testMapRequest(): void
    {
        $user = $this->createUser();

        $this->loginAs($user);

        $response = $this->postJson('/api/test-map-request', ['id' => 1, 'name' => 'Custom name']);

        $response->assertStatus(200);

        // Get the full response and use array access for reliability
        $responseData = $response->json();
        self::assertIsArray($responseData, 'Response should be an array');
        self::assertIsArray($responseData['data'], 'Data should be an array');

        self::assertEquals(1, $responseData['data']['id']);
        self::assertEquals('Custom name', $responseData['data']['name']);
        self::assertEquals('Маппинг запроса выполнен успешно', $responseData['message']);
    }
}
