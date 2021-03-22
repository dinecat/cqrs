<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Command;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Command message bus.
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
     * Performs command and returns result.
     */
    public function command(CommandInterface $command): mixed
    {
        return $this->handle($command);
    }
}
