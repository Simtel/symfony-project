<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Entity\Block;
use App\Context\User\Domain\Entity\Contact;
use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;
use App\Tests\Feature\FeatureTestBaseCase;
use DateTimeImmutable;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class UserTest extends FeatureTestBaseCase
{
    /**
     * @throws Exception
     */
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

        if ($userFromDb === null) {
            self::fail('Entity not found');
        }

        self::assertSame(
            [
                $userFromDb->getBlock()->getStartDate()?->format('c'),
                $userFromDb->getBlock()->getEndDate()?->format('c'),
            ],
            [
                $user->getBlock()->getStartDate()?->format('c'),
                $user->getBlock()->getEndDate()?->format('c'),
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
        $user2 = $this->createUser(['email' => 'user2@email.com', 'name' => 'Second User']);
        $user3 = $this->createUser(['email' => 'user3@email.com', 'name' => 'Third User']);
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

        /** @var  UserRepositoryInterface $userRepository */
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

    /**
     * @throws ORMException
     */
    public function testRemoveUser(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();
        $location = new Location('Amsterdam');
        $location2 = new Location('Moscow');

        $user->addLocation($location);

        $em->persist($location);
        $em->persist($location2);
        $em->flush();

        $locationRepository = $em->getRepository(Location::class);
        $userRepository = $em->getRepository(User::class);

        self::assertCount(2, $locationRepository->findAll());
        self::assertCount(1, $userRepository->findAll());

        $em->remove($user);
        $em->flush();

        self::assertCount(2, $locationRepository->findAll());
        self::assertCount(0, $userRepository->findAll());

    }

    /**
     * @throws ORMException
     * @throws Exception
     */
    public function testRemoveUserWithContacts(): void
    {
        $em = $this->getEntityManager();
        $user = $this->createUser();
        $contact = new Contact($user,'email','Email','s@s.com');
        $contact2 = new Contact($user,'phone','Phone', '13465');

        $em->persist($contact);
        $em->persist($contact2);
        $em->flush();

        $contactRepository = $em->getRepository(Contact::class);
        $userRepository = $em->getRepository(User::class);

        self::assertCount(2, $contactRepository->findAll());
        self::assertCount(1, $userRepository->findAll());

        $em->remove($user);
        $em->flush();

        self::assertCount(0, $contactRepository->findAll());
        self::assertCount(0, $userRepository->findAll());

    }
}
