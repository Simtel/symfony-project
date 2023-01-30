<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Entity\Location;
use App\Tests\UnitTest;

class UserLocationTest extends UnitTest
{
    public function testOrderLocations(): void
    {
        $user = $this->createUser();
        $location = new Location('Amsterdam');
        $location2 = new Location('Moscow');
        $location3 = new Location('Ulyanovsk');

        $user->addLocation($location3);
        $user->addLocation($location2);
        $user->addLocation($location);

        $this->entityManager->persist($location3);
        $this->entityManager->persist($location);
        $this->entityManager->persist($location2);


        $this->entityManager->flush();

        $this->entityManager->refresh($user);

        self::assertSame(
            ['Amsterdam', 'Moscow', 'Ulyanovsk'],
            array_map(
                static fn (Location $location): string => $location->getName(),
                $user->getLocations()
            )
        );
    }
}
