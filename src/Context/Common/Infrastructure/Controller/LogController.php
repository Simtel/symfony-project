<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use App\Context\Common\Domain\Contract\LogRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LogController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: '/api/log/list', name: 'log_list_view', methods: ['GET'])]
    public function list(
        LogRepositoryInterface $logRepository,
        Environment $twig,
    ): JsonResponse {
        $out = [];

        $logs = $logRepository->findAll();
        foreach ($logs as $log) {
            $out[] = $twig->render('Logs/base_view.html.twig', [
                'user' => $log->getAuthor()->getName(),
                'action' => $log->getAction(),
                'createdAt' => $log->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        }

        return new JsonResponse($out);
    }
}
