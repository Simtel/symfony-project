<?php

namespace App\Tests\Feature;

use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigTest extends WebTestCase
{
    public function testAccessDenied(): void
    {
        static::createClient()->request('GET', '/api/config/list');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws JsonException
     */
    public function testAllowAccess(): void
    {
        $client = static::createClient();

        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = new User('test@mail.com', 'Test', '456');
        $user->setToken(123);
        $em->persist($user);
        $em->flush();

        $client->request(
            'GET',
            '/api/config/list',
            [],
            [],
            ['HTTP_X_AUTH_TOKEN' => $user->getToken()]
        );
        $response = $client->getResponse();
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(
            ['configs' => []],
            json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    public function testGetConfigs(): void
    {
        $client = static::createClient();

        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = new User('test@mail.com', 'Test', '456');
        $user->setToken(123);
        $em->persist($user);

        $config = new Config('app', 'Test App', $user);
        $em->persist($config);

        $em->flush();

        $client->request(
            'GET',
            '/api/config/list',
            [],
            [],
            ['HTTP_X_AUTH_TOKEN' => $user->getToken()]
        );
        $response = $client->getResponse();
        self::assertSame(200, $response->getStatusCode());
        self::assertSame([
            'configs' => [
                [
                    'name' => $config->getName(),
                    'value' => $config->getValue(),
                    'updatedAt' => $config->getUpdateAt()->format('Y-m-d H:i:s'),
                    'user' => [
                        'name' => $config->getCreatedBy()->getName(),
                        'id' => $config->getCreatedBy()->getId(),
                    ]
                ]
            ]
        ], json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }
}
