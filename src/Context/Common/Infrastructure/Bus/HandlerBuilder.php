<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Bus;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class HandlerBuilder
{
    /**
     * @throws ReflectionException
     */
    public static function fromCallables(iterable $callables): array
    {
        $callablesHandlers = [];

        foreach ($callables as $callable) {
            $envelop = self::extractFirstParam($callable);

            if (!array_key_exists((int)$envelop, $callablesHandlers)) {
                $callablesHandlers[self::extractFirstParam($callable)] = [];
            }

            $callablesHandlers[self::extractFirstParam($callable)][] = $callable;
        }

        return $callablesHandlers;
    }

    /**
     * @param class-string|object $class
     * @throws ReflectionException
     */
    private static function extractFirstParam(object|string $class): string|null
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod('__invoke');

        if ($method->getNumberOfParameters() === 1) {
            /** @var ReflectionNamedType|null $type */
            $type = $method->getParameters()[0]->getType();
            return $type?->getName();
        }

        return null;
    }
}
