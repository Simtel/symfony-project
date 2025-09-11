<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionEventListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();


        if (
            $exception instanceof ValidationFailedException
            && $exception->getViolations()->count() > 0
        ) {
            $errors = [];
            foreach ($exception->getViolations() as $violation) {
                $errors[] = str_replace(['[', ']'], '', $violation->getPropertyPath()) . ': ' . $violation->getMessage(
                );
            }
            $response = new JsonResponse(
                [
                    'errors' => $errors
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } else {
            $response = new JsonResponse(['errors' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        }

        $event->setResponse($response);
    }
}
