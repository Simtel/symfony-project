<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Контроллер для работы с email сообщениями
 */
final class EmailController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $normalizer,
        private readonly MailerInterface $mailer,
    ) {
        parent::__construct($normalizer);
    }

    /**
     * Тестирование отправки email сообщения
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/api/test-email', name: 'test_email', methods: ['GET'])]
    public function testEmail(): JsonResponse
    {
        try {
            $email = (new Email())
                ->from('noreply@mail.com')
                ->to('simtel@example.com')
                ->subject('Test Email!')
                ->text('Send test email for me!')
                ->html('<p>Send html email!</p>');

            $this->mailer->send($email);

            return $this->success([
                'message' => 'Email успешно отправлен',
                'test-email' => true,
                'time' => new DateTimeImmutable()
            ]);
        } catch (TransportExceptionInterface $e) {
            return $this->error('Ошибка при отправке email: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->error('Неожиданная ошибка при отправке email: ' . $e->getMessage());
        }
    }
}
