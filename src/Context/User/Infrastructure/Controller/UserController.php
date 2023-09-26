<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\Common\Infrastructure\Contract\CommandBusInterface;
use App\Context\User\Application\Command\UpdateUserCommand;
use App\Context\User\Application\Dto\UpdateUserDto;
use App\Context\User\Domain\Contract\UserServiceInterface;
use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;
use App\Context\User\Infrastructure\View\UserFullView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/user/{user}', name: 'show_user', methods: ['GET'])]
    public function show(
        User $user,
    ): JsonResponse {
        return new JsonResponse($this->normalizer->normalize(new UserFullView($user)));
    }

    #[Route(path: '/api/user/{user}/location/{location}', name: 'add_location_to_user', methods: ['PUT'])]
    public function addLocation(
        User $user,
        Location $location,
        EntityManagerInterface $entityManager,
        CommandBusInterface $commandBus,
    ): JsonResponse {
        $dto = new UpdateUserDto($user);
        $dto->addLocation($location);

        $commandBus->dispatch(new UpdateUserCommand($dto));

        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route(path: '/api/user/{user}/calculate-access', name: 'calculate_access', methods: ['POST'])]
    public function calculateAccess(USer $user, UserServiceInterface $userService): JsonResponse
    {
        $userService->calculateAccesses($user);

        return new JsonResponse([], Response::HTTP_CREATED);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/user/find/{userName}', name: 'show_user_by_name', methods: ['GET'])]
    public function showByName(
        #[MapEntity(mapping: ['userName' => 'name'])]
        User $user,
    ): JsonResponse {
        return new JsonResponse($this->normalizer->normalize(new UserFullView($user)));
    }
}
