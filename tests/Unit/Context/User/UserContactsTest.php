<?php

declare(strict_types=1);

namespace App\Tests\Unit\Context\User;

use App\Context\User\Domain\Entity\Contact;
use App\Context\User\Domain\Entity\User;
use App\Tests\Feature\FeatureTestBaseCase;
use Exception;

class UserContactsTest extends FeatureTestBaseCase
{
    /**
     * @throws Exception
     */
    public function testArrayContactsIncludeCodeAsKeys(): void
    {
        $em = $this->getEntityManager();
        $user = new User('s@mail.com', 'Test', '134', '33333');

        $contactFirst = new Contact($user, 'first', 'First', '1');
        $contactSecond = new Contact($user, 'second', 'second', '1');

        $user->addContact($contactFirst);
        $user->addContact($contactSecond);

        $em->persist($user);
        $em->persist($contactFirst);
        $em->persist($contactSecond);
        $em->flush();

        $em->refresh($user);

        self::assertSame(
            [
                'first',
                'second',
            ],
            array_keys($user->getContacts())
        );
    }
}
