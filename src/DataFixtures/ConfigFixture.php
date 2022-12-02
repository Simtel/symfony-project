<?php

namespace App\DataFixtures;

use App\Context\Common\Domain\Entity\Config;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfigFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $config = new Config('app', 'Test Project');
        $manager->persist($config);

        $manager->flush();
    }
}
