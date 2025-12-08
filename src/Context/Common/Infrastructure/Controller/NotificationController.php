<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Контроллер для работы с уведомлениями
 */
final class NotificationController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $normalizer,
    ) {
        parent::__construct($normalizer);
    }

    /**
     * Тестирование отправки уведомления
     *
     * @param ChatterInterface $chatter
     * @return JsonResponse
     */
    #[Route('/api/test-notify', name: 'test_notify', methods: ['GET'])]
    public function testNotify(ChatterInterface $chatter): JsonResponse
    {
        try {
            $message = new ChatMessage('Notification from symfony project')
                ->transport('telegram');

            $sentMessage = $chatter->send($message);

            return $this->success([
                'message' => 'Уведомление успешно отправлено',
                'messageId' => $sentMessage?->getMessageId()
            ]);
        } catch (TransportExceptionInterface $e) {
            return $this->error('Ошибка при отправке уведомления: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->error('Неожиданная ошибка при отправке уведомления: ' . $e->getMessage());
        }
    }
}
