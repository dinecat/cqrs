<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Command;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Bus for command messages.
 *
 * For performance & RAD purposes command bus can return result of command execution.
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
