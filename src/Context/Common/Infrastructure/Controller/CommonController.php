<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommonController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly MailerInterface $mailer
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json(
            $this->normalizer->normalize(
                ['test' => true, 'time' => new DateTimeImmutable()]
            )
        );
    }

    /**
     * @throws ExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/api/test-email', name: 'test-email', methods: ['GET'])]
    public function testEmail(): JsonResponse
    {
        $email = (new Email())
            ->from('noreply@mail.com')
            ->to('simtel@example.com')
            ->subject('Test Email!')
            ->text('Send test email for me!')
            ->html('<p>Send html email!</p>');

        $this->mailer->send($email);

        return $this->json(
            $this->normalizer->normalize(
                ['test-email' => true, 'time' => new DateTimeImmutable()]
            )
        );
    }

    /**
     * @throws \Symfony\Component\Notifier\Exception\TransportExceptionInterface
     */
    #[Route('/api/test-notify', name: 'test-notify', methods: ['GET'])]
    public function testNotify(ChatterInterface $chatter): JsonResponse
    {
        $message = (new ChatMessage('Notification from symfony project'))
            ->transport('telegram');

        $sentMessage = $chatter->send($message);

        return new JsonResponse(['messageId' => $sentMessage?->getMessageId()]);
    }
}
