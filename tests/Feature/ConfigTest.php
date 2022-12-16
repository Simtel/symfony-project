<?php

namespace App\Tests\Feature;

use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use Exception;

class ConfigTest extends FeatureTest
{
    public function testAccessDenied(): void
    {
        $this->json('/api/config/list');
        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @throws Exception
     */
    public function testAllowAccess(): void
    {
        $em = $this->getEntityManager();

        $user = new User('test@mail.com', 'Test', '456');
        $user->setToken(123);
        $em->persist($user);
        $em->flush();

        $this->loginAs($user);

        $response = $this->json('/api/config/list');

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
        $user->setToken(123);
        $em->persist($user);

        $config = new Config('app', 'Test App', $user);
        $em->persist($config);

        $em->flush();

        $this->loginAs($user);

        $response = $this->json('/api/config/list');

        self::assertSame(200, $response->getStatusCode());
        $this->assertResponse($response, 'Common/configs');
    }
}
