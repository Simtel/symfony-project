<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Context\Common\Domain\Entity\Log;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class LogTest extends FeatureTest
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \JsonException
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

        self::assertResponseStatusCodeSame(200);
        $this->assertResponse($response, 'Log/log_view_list');
    }
}
