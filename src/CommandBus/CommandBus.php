<?php

declare(strict_types=1);

namespace Dinecat\Messenger\CommandBus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Decorator for command message bus.
 * For performance & RAD purposes command bus can return values.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class CommandBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandMessageBus)
    {
        $this->messageBus = $commandMessageBus;
    }

    /**
     * @return mixed The handler returned value
     */
    public function command(CommandMessageInterface $command)
    {
        return $this->handle($command);
    }
}
