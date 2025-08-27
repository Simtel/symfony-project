<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use App\Context\Common\Application\Dto\TestMapRequestDto;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Общие тестовые методы API
 */
class CommonController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $normalizer,
    ) {
        parent::__construct($normalizer);
    }

    /**
     * Общий тестовый endpoint
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route('/api/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        try {
            return $this->success([
                'test' => true,
                'time' => new DateTimeImmutable(),
                'message' => 'API работает корректно'
            ]);
        } catch (\Exception $e) {
            return $this->error('Ошибка при выполнении теста: ' . $e->getMessage());
        }
    }

    /**
     * Тестирование маппинга запроса
     *
     * @param TestMapRequestDto $dto DTO запроса
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route('/api/test-map-request', name: 'test_map_request', methods: ['POST'])]
    public function testMapRequest(
        #[MapRequestPayload] TestMapRequestDto $dto,
    ): JsonResponse {
        try {
            return $this->success([
                'message' => 'Маппинг запроса выполнен успешно',
                'data' => $dto
            ]);
        } catch (\Exception $e) {
            return $this->error('Ошибка при маппинге запроса: ' . $e->getMessage());
        }
    }
}
