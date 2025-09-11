<?php

namespace App\DataFixtures;

use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ConfigFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user =  $this->getReference(UserFixture::USER_REFERENCE, User::class);
        $config = new Config('app', 'Test Project', $user);
        $manager->persist($config);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class
        ];
    }
}
