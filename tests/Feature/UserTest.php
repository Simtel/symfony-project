<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Context\User\Domain\Entity\Location;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class UserTest extends FeatureTest
{
    /**
     * @throws ORMException
     * @throws Exception
     */
    public function testShowUser(): void
    {
        $em = $this->getEntityManager();

        $user = $this->createUser();

        $location = new Location('Moscow');
        $em->persist($location);

        $user->addLocation($location);

        $em->flush();

        $this->loginAs($user);
        $response = $this->getJson('/api/user/' . $user->getId());

        self::assertResponseStatusCodeSame(200);
        $this->assertResponse($response, 'User/user_full_view');
    }
}
