<?php

namespace App\DataFixtures;

use App\Context\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const USER_REFERENCE = 'admin-user';

    public function load(ObjectManager $manager): void
    {
        $user = new User('test@test.com', 'Test', '123456');
        $user->setToken('123333');
        $manager->persist($user);

        $manager->flush();
        $this->addReference(self::USER_REFERENCE, $user);
    }
}
