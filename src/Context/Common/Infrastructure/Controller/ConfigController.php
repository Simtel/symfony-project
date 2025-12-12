<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use App\Context\Common\Application\Dto\CreateConfigDto;
use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Contract\UserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Контроллер для управления конфигурациями
 */
final class ConfigController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $serializer,
        private readonly ConfigProviderInterface $configProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserProviderInterface $userProvider,
    ) {
        parent::__construct($serializer);
    }

    /**
     * Получение списка всех конфигураций
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/config/list', name: 'list_config', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $configs = $this->configProvider->getList();

            return $this->success($configs);
        } catch (\Exception $e) {
            return $this->error('Ошибка при получении списка конфигураций: ' . $e->getMessage());
        }
    }

    /**
     * Создание новой конфигурации
     *
     * @param CreateConfigDto $dto Данные для создания конфигурации
     * @return JsonResponse
     */
    #[Route(path: '/api/config', name: 'create_config', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateConfigDto $dto): JsonResponse
    {
        try {
            // Проверка обязательных полей
            if (empty($dto->getName())) {
                return $this->validationError(
                    ['name' => 'Имя конфигурации не может быть пустым'],
                    'Ошибка валидации данных'
                );
            }

            if ($dto->getValue() === null || $dto->getValue() === '') {
                return $this->validationError(
                    ['value' => 'Значение конфигурации не может быть пустым'],
                    'Ошибка валидации данных'
                );
            }

            try {
                $currentUser = $this->userProvider->getCurrentUser();
            } catch (\RuntimeException $e) {
                return $this->unauthorized('Необходима авторизация для создания конфигурации');
            }

            $config = new Config($dto->getName(), $dto->getValue(), $currentUser);

            $this->entityManager->persist($config);
            $this->entityManager->flush();

            return $this->created([
                'message' => 'Конфигурация успешно создана',
                'config' => [
                    'id' => $config->getId(),
                    'name' => $config->getName(),
                    'value' => $config->getValue()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error('Ошибка при создании конфигурации: ' . $e->getMessage());
        }
    }

    /**
     * Удаление конфигурации по ID
     *
     * @param string $id UUID конфигурации для удаления
     * @return JsonResponse
     */
    #[Route(path: '/api/config/{id}', name: 'delete_config', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        try {
            // Проверяем формат UUID
            if (!Uuid::isValid($id)) {
                return $this->validationError(
                    ['id' => 'Некорректный формат ID конфигурации'],
                    'Ошибка валидации данных'
                );
            }

            try {
                $uuid = Uuid::fromString($id);
                $this->configProvider->delete($uuid);

                return $this->success([
                    'message' => 'Конфигурация успешно удалена'
                ]);
            } catch (EntityNotFoundException $e) {
                return $this->notFound('Конфигурация с указанным ID не найдена');
            }
        } catch (\Exception $e) {
            return $this->error('Ошибка при удалении конфигурации: ' . $e->getMessage());
        }
    }
}
