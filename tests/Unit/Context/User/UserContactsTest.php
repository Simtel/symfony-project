<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Entity\Contact;
use App\Context\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserContactsTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    public function testArrayContactsIncludeCodeAsKeys(): void
    {
        $user = new User('s@mail.com', 'Test', '134');
        $user->setToken('33333');

        $contactFirst = new Contact($user, 'first', 'First', '1');
        $contactSecond = new Contact($user, 'second', 'second', '1');

        $user->addContact($contactFirst);
        $user->addContact($contactSecond);

        $this->entityManager->persist($user);
        $this->entityManager->persist($contactFirst);
        $this->entityManager->persist($contactSecond);
        $this->entityManager->flush();

        $this->entityManager->refresh($user);

        self::assertSame(
            [
                'first',
                'second',
            ],
            array_keys($user->getContacts())
        );
    }
}
