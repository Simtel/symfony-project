<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Context\Common\Domain\Entity\Log;
use App\Context\User\Domain\Entity\Location;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function testAddLocationToUser(): void
    {
        $em = $this->getEntityManager();

        $user = $this->createUser();
        $location = new Location('Moscow');
        $em->persist($location);
        $user->addLocation($location);


        $newLocation = new Location('Ulyanovsk');
        $em->persist($newLocation);

        $em->flush();

        $this->loginAs($user);

        $this->putJson('/api/user/' . $user->getId() . '/location/' . $newLocation->getId());

        $em->refresh($user);

        self::assertSame(
            [
                [
                    'name' => 'Moscow',
                ],
                [
                    'name' => 'Ulyanovsk'
                ]
            ],
            array_map(
                static fn (Location $location): array => ['name' => $location->getName()],
                $user->getLocations()
            )
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function testAddLocationToUserLogCreated(): void
    {
        $em = $this->getEntityManager();

        $user = $this->createUser();
        $location = new Location('Moscow');
        $em->persist($location);
        $user->addLocation($location);


        $newLocation = new Location('Ulyanovsk');
        $em->persist($newLocation);

        $em->flush();

        $this->loginAs($user);

        $this->putJson('/api/user/' . $user->getId() . '/location/' . $newLocation->getId());

        $repository = $em->getRepository(Log::class);
        $logs = $repository->findAll();

        self::assertSame(
            [
                [
                    'action' => 'Ulyanovsk has been added to ' . $user->getName(),
                    'author' => $user->getId(),
                ]
            ],
            array_map(
                static fn (Log $log): array => ['action' => $log->getAction(), 'author' => $log->getAuthor()->getId()],
                $logs
            )
        );
    }
}
