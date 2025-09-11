<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use App\Context\Common\Application\Dto\CreateConfigDto;
use App\Context\Common\Domain\Entity\Config;
use App\Context\User\Domain\Contract\UserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Контроллер для управления конфигурациями
 */
final class ConfigController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $normalizer,
        private readonly ConfigProviderInterface $configProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserProviderInterface $userProvider,
    ) {
        parent::__construct($normalizer);
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
}
