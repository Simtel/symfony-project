<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Domain\Entity\User;
use App\Context\User\Infrastructure\View\UserFullView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
