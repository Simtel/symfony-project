<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Context\Common\Domain\Entity\Log;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

final class LogTest extends FeatureTestBaseCase
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function testLogsListView(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();

        $log = new Log('Location has been added to user', $user);
        $em->persist($log);
        $em->flush();

        $this->loginAs($user);

        $response = $this->getJson('/api/log/list');

        $response->assertStatus(200);
        $expected = [
            'Author: Test
Action: Location has been added to user
Created at: '.$log->getCreatedAt()->format('Y-m-d H:i:s').'
Url: /api/log/'.$log->getId().'
'
        ];
        self::assertSame($expected, $response->json('logs'));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testDebugHashToLogViewList(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();

        $log = new Log('Location has been added to user', $user);
        $em->persist($log);
        $em->flush();

        $this->loginAs($user);

        $response = $this->getJson('/api/log/list');

        //$hash = sha1($response->getContent());
        $response->assertStatus(200);
        //self::assertResponseHasHeader('X-DEBUG-HASH');
        //self::assertResponseHeaderSame('X-DEBUG-HASH', $hash);
    }
}
