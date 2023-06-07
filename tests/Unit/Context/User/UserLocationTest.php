<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Entity\Location;
use App\Tests\Feature\FeatureTestBaseCase;

class UserLocationTest extends FeatureTestBaseCase
{
    public function testOrderLocations(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();
        $location = new Location('Amsterdam');
        $location2 = new Location('Moscow');
        $location3 = new Location('Ulyanovsk');

        $user->addLocation($location3);
        $user->addLocation($location2);
        $user->addLocation($location);

        $em->persist($location3);
        $em->persist($location);
        $em->persist($location2);


        $em->flush();

        $em->refresh($user);

        self::assertSame(
            ['Amsterdam', 'Moscow', 'Ulyanovsk'],
            array_map(
                static fn (Location $location): string => $location->getName(),
                $user->getLocations()
            )
        );
    }
}
