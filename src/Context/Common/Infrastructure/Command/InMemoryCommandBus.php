<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Command;

use App\Context\Common\Domain\Contract\CommandInterface;
use App\Context\Common\Infrastructure\Bus\HandlerBuilder;
use App\Context\Common\Infrastructure\Contract\CommandBusInterface;
use InvalidArgumentException;
use ReflectionException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Throwable as ThrowableAlias;

class InMemoryCommandBus implements CommandBusInterface
{
    private MessageBus $bus;

    /**
     * @throws ReflectionException
     */
    public function __construct(
        iterable $commandHandlers
    ) {
        $this->bus = new MessageBus([
            new HandleMessageMiddleware(
                new HandlersLocator(
                    HandlerBuilder::fromCallables($commandHandlers),
                ),
            ),
        ]);
    }

    /**
     * @throws ThrowableAlias
     */
    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->bus->dispatch($command);
        } catch (NoHandlerForMessageException $e) {
            throw new InvalidArgumentException(sprintf('The command has not a valid handler: %s', $command::class));
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() !== null) {
                throw $e->getPrevious();
            }
            throw new \RuntimeException('Error dispatch command:' . get_class($command));
        }
    }
}
