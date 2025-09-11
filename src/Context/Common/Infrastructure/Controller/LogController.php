<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use App\Context\Common\Application\Contract\LogProviderInterface;
use App\Context\Common\Domain\Entity\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Контроллер для управления логами
 */
final class LogController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $normalizer,
        private readonly LogProviderInterface $logProvider,
        private readonly Environment $twig,
    ) {
        parent::__construct($normalizer);
    }
    /**
     * Получение списка логов с рендерингом
     *
     * @return JsonResponse
     */
    #[Route(path: '/api/log/list', name: 'log_list_view', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $logs = $this->logProvider->getList();

            if (empty($logs)) {
                return $this->success(['logs' => [], 'message' => 'Логи не найдены']);
            }

            $renderedLogs = [];

            foreach ($logs as $log) {
                try {
                    $renderedLogs[] = $this->twig->render('Logs/base_view.html.twig', [
                        'user' => $log->getAuthor()->getName(),
                        'action' => $log->getAction(),
                        'createdAt' => $log->getCreatedAt()->format('Y-m-d H:i:s'),
                        'url' => $this->generateUrl('log_view', ['log' => $log->getId()]),
                    ]);
                } catch (LoaderError|RuntimeError|SyntaxError $e) {
                    // Если ошибка рендеринга, возвращаем простые данные
                    $renderedLogs[] = [
                        'id' => $log->getId(),
                        'user' => $log->getAuthor()->getName(),
                        'action' => $log->getAction(),
                        'createdAt' => $log->getCreatedAt()->format('Y-m-d H:i:s'),
                        'render_error' => true
                    ];
                }
            }

            return $this->success(['logs' => $renderedLogs]);
        } catch (\Exception $e) {
            return $this->error('Ошибка при получении списка логов: ' . $e->getMessage());
        }
    }

    /**
     * Получение детальной информации о логе
     *
     * @param Log $log Лог
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/log/{log}', name: 'log_view', methods: ['GET'])]
    public function show(Log $log): JsonResponse
    {
        try {
            return $this->success([
                'log' => $log,
                'author' => $log->getAuthor()->getName(),
                'createdAt' => $log->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->error('Ошибка при получении информации о логе: ' . $e->getMessage());
        }
    }
}
