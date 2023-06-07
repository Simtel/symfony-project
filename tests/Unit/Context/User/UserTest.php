<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Entity\Block;
use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;
use App\Tests\Feature\FeatureTestBaseCase;
use DateTimeImmutable;
use Exception;

class UserTest extends FeatureTestBaseCase
{
    public function testSecretKey(): void
    {
        $user = new User('test@mail.com', 'Test', '123');

        $this->getEntityManager()->persist($user);

        self::assertSame(sha1('Test' . '/' . '123'), $user->getSecretKey());
    }

    /**
     * @throws Exception
     */
    public function testBlockedDateRange(): void
    {
        $user = new User('test@mail.com', 'Test', '123');
        $user->setToken('333');
        $user->setBlock(
            new Block(
                new DateTimeImmutable('2022-01-21 13:56:50'),
                new DateTimeImmutable('2022-01-26 12:00:00')
            )
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $userFromDb = $userRepository->find($user->getId());

        self::assertSame(
            [
                $userFromDb->getBlock()->getStartDate()->format('c'),
                $userFromDb->getBlock()->getEndDate()->format('c'),
            ],
            [
                $user->getBlock()->getStartDate()->format('c'),
                $user->getBlock()->getEndDate()->format('c'),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function testOrderLocations(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();
        $user2 = $this->createUser(['email' => 'user2@email.com','name' => 'Second User']);
        $user3 = $this->createUser(['email' => 'user3@email.com','name' => 'Third User']);
        $location = new Location('Amsterdam');
        $location2 = new Location('Moscow');
        $location3 = new Location('Ulyanovsk');

        $em->persist($location3);
        $em->persist($location);
        $em->persist($location2);

        $user->addLocation($location);
        $user->addLocation($location3);
        $user2->addLocation($location2);
        $user3->addLocation($location3);

        $em->flush();


        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $users = $userRepository->findByLocation($location3);

        self::assertSame(
            [$user->getId(), $user3->getId()],
            array_map(
                static fn (User $user): int => $user->getId(),
                $users
            )
        );
    }
}
