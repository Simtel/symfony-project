<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Entity\Block;
use App\Context\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSecretKey(): void
    {
        $user = new User('test@mail.com', 'Test', '123');

        $this->entityManager->persist($user);

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

        $this->entityManager->persist($user);
        $this->entityManager->flush();

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
}
