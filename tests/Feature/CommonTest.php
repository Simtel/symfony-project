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

        self::assertEquals(1, $response->json('id'));
        self::assertEquals('Custom name', $response->json('name'));
    }
}
