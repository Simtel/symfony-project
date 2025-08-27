<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Базовый API контроллер с общими методами для формирования ответов
 */
abstract class BaseApiController extends AbstractController
{
    public function __construct(
        protected readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * Создает JSON ответ с нормализованными данными
     *
     * @param mixed $data Данные для нормализации
     * @param int $status HTTP статус код
     * @param array<string, string> $headers HTTP заголовки
     * @param string $context Контекст нормализации
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    protected function createJsonResponse(
        mixed $data,
        int $status = Response::HTTP_OK,
        array $headers = [],
        string $context = 'array'
    ): JsonResponse {
        $normalizedData = $this->normalizer->normalize($data, $context);

        return new JsonResponse($normalizedData, $status, $headers);
    }

    /**
     * Создает успешный JSON ответ
     *
     * @param mixed $data Данные для ответа
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    protected function success(mixed $data = [], array $headers = []): JsonResponse
    {
        return $this->createJsonResponse($data, Response::HTTP_OK, $headers);
    }

    /**
     * Создает JSON ответ для созданного ресурса
     *
     * @param mixed $data Данные созданного ресурса
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    protected function created(mixed $data = [], array $headers = []): JsonResponse
    {
        return $this->createJsonResponse($data, Response::HTTP_CREATED, $headers);
    }

    /**
     * Создает JSON ответ для отсутствующего контента
     *
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     */
    protected function noContent(array $headers = []): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT, $headers);
    }

    /**
     * Создает JSON ответ с ошибкой
     *
     * @param string $message Сообщение об ошибке
     * @param int $status HTTP статус код
     * @param array<string, mixed> $details Дополнительные детали ошибки
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     */
    protected function error(
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        array $details = [],
        array $headers = []
    ): JsonResponse {
        $errorData = [
            'error' => true,
            'message' => $message,
            'status' => $status,
        ];

        if (!empty($details)) {
            $errorData['details'] = $details;
        }

        return new JsonResponse($errorData, $status, $headers);
    }

    /**
     * Создает JSON ответ для валидационных ошибок
     *
     * @param array<string, mixed> $violations Нарушения валидации
     * @param string $message Общее сообщение об ошибке
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     */
    protected function validationError(
        array $violations,
        string $message = 'Validation failed',
        array $headers = []
    ): JsonResponse {
        return $this->error(
            $message,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            ['violations' => $violations],
            $headers
        );
    }

    /**
     * Создает JSON ответ для ошибки "не найдено"
     *
     * @param string $message Сообщение об ошибке
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     */
    protected function notFound(string $message = 'Resource not found', array $headers = []): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND, [], $headers);
    }

    /**
     * Создает JSON ответ для ошибки авторизации
     *
     * @param string $message Сообщение об ошибке
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     */
    protected function unauthorized(string $message = 'Unauthorized', array $headers = []): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED, [], $headers);
    }

    /**
     * Создает JSON ответ для ошибки доступа
     *
     * @param string $message Сообщение об ошибке
     * @param array<string, string> $headers HTTP заголовки
     * @return JsonResponse
     */
    protected function forbidden(string $message = 'Forbidden', array $headers = []): JsonResponse
    {
        return $this->error($message, Response::HTTP_FORBIDDEN, [], $headers);
    }
}
