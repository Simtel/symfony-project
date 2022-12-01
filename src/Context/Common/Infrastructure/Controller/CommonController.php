<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommonController extends AbstractController
{

    public function __construct(
        private readonly NormalizerInterface $normalizer
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/test', name: 'test')]
    public function test(): JsonResponse
    {
        return $this->json(
            $this->normalizer->normalize(
                ['test' => true, 'time' => new DateTimeImmutable()]
            )
        );
    }

}
