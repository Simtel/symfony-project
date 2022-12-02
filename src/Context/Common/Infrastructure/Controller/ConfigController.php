<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use App\Context\Common\Application\Contract\ConfigProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConfigController extends AbstractController
{
    public function __construct(
        private readonly ConfigProviderInterface $configProvider,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/config/list', name: 'list_config', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->normalizer->normalize($this->configProvider->getList(), 'array'));
    }
}
