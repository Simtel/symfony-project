<?php

namespace App\DataFixtures;

use App\Context\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixture extends Fixture
{
    public const string USER_REFERENCE = 'admin-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User('test@test.com', 'Test', '', '123333');
        $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $manager->flush();
        $this->addReference(self::USER_REFERENCE, $user);
    }
}
