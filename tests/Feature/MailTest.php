<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Doctrine\ORM\Exception\ORMException;

class MailTest extends FeatureTestBaseCase
{
    /**
     * @throws ORMException
     */
    public function testSendEmail(): void
    {
        $user = $this->createUser();

        $this->loginAs($user);

        $this->getJson('/api/test-email');

        self::assertEmailCount(1);

        $email = self::getMailerMessage();
        if ($email === null) {
            self::fail('Message not found');
        }
        self::assertEmailAddressContains($email, 'to', 'simtel@example.com');
        self::assertEmailAddressContains($email, 'from', 'noreply@mail.com');
        self::assertEmailHtmlBodyContains($email, '<p>Send html email!</p>');
        self::assertEmailTextBodyContains($email, 'Send test email for me!');
    }
}
