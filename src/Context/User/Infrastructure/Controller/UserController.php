<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\Common\Infrastructure\Controller\BaseApiController;
use App\Context\User\Application\CQRS\Command\UpdateUserCommand;
use App\Context\User\Application\Dto\UpdateUserDto;
use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Contract\UserServiceInterface;
use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;
use App\Context\User\Domain\Event\LocationAddedEvent;
use App\Context\User\Infrastructure\View\UserFullView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Контроллер для управления пользователями
 */
final class UserController extends BaseApiController
{
    public function __construct(
        NormalizerInterface $normalizer,
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserServiceInterface $userService,
        private readonly UserRepositoryInterface $userRepository,
    ) {
        parent::__construct($normalizer);
    }

    /**
     * Получение информации о пользователе
     *
     * @param User $user Пользователь
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/user/{user}', name: 'show_user', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        try {
            $userView = new UserFullView($user);
            return $this->success($userView);
        } catch (\Exception $e) {
            return $this->error('Ошибка при получении информации о пользователе: ' . $e->getMessage());
        }
    }

    /**
     * Добавление локации к пользователю
     *
     * @param User $user Пользователь
     * @param Location $location Локация
     * @return JsonResponse
     */
    #[Route(path: '/api/user/{user}/location/{location}', name: 'add_location_to_user', methods: ['PUT'])]
    public function addLocation(User $user, Location $location): JsonResponse
    {
        try {
            $dto = new UpdateUserDto($user);
            $dto->addLocation($location);

            $this->messageBus->dispatch(new UpdateUserCommand($dto));
            $this->messageBus->dispatch(
                new LocationAddedEvent('Location added'),
                [new AmqpStamp('user.events.add_location')]
            );

            $this->entityManager->flush();

            return $this->success(['message' => 'Локация успешно добавлена к пользователю']);
        } catch (\Exception $e) {
            return $this->error('Ошибка при добавлении локации: ' . $e->getMessage());
        }
    }

    /**
     * Расчет доступов пользователя
     *
     * @param User $user Пользователь
     * @return JsonResponse
     */
    #[Route(path: '/api/user/{user}/calculate-access', name: 'calculate_access', methods: ['POST'])]
    public function calculateAccess(User $user): JsonResponse
    {
        try {
            $this->userService->calculateAccesses($user);

            return $this->created(['message' => 'Доступы пользователя успешно рассчитаны']);
        } catch (\Exception $e) {
            return $this->error('Ошибка при расчете доступов: ' . $e->getMessage());
        }
    }

    /**
     * Поиск пользователя по имени
     *
     * @param User $user Пользователь (найденный по имени)
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/user/find/{userName}', name: 'show_user_by_name', methods: ['GET'])]
    public function showByName(
        #[MapEntity(mapping: ['userName' => 'name'])]
        User $user,
    ): JsonResponse {
        try {
            $userView = new UserFullView($user);
            return $this->success($userView);
        } catch (\Exception $e) {
            return $this->error('Ошибка при поиске пользователя: ' . $e->getMessage());
        }
    }

    /**
     * Получение пользователей по локации
     *
     * @param Location $location Локация
     * @return JsonResponse
     */
    #[Route(path: '/api/users/{location}', name: 'users_by_location', methods: ['GET'])]
    public function usersByLocation(Location $location): JsonResponse
    {
        try {
            $users = $this->userRepository->findByLocation($location);

            if (empty($users)) {
                return $this->success(['users' => [], 'message' => 'Пользователи в данной локации не найдены']);
            }

            return $this->success(['users' => $users]);
        } catch (\Exception $e) {
            return $this->error('Ошибка при поиске пользователей по локации: ' . $e->getMessage());
        }
    }
}
