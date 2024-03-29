<?php

declare(strict_types=1);

namespace App\Tests;

use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class UnitTestBaseCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        /** @var ManagerRegistry $manager */
        $manager = $kernel->getContainer()->get('doctrine');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $manager->getManager();
        $this->entityManager = $entityManager;
    }

    /**
     * @param string[] $overrides
     * @return User
     */
    public function createUser(array $overrides = []): User
    {
        $default = ['email' => 'test@mail.com', 'name' => 'Test', 'password' => '456', 'token' => '4444'];
        $attributes = array_replace($default, $overrides);

        $user = new User($attributes['email'], $attributes['name'], $attributes['password']);
        $user->setToken($attributes['token']);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return $user;
    }
}
