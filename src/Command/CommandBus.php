<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Command;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

use function count;

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
     * Performs command and returns result for synchronous commands.
     *
     * @param array<StampInterface> $stamps
     */
    public function command(CommandInterface $command, array $stamps = []): mixed
    {
        if ($command instanceof AsyncCommandInterface) {
            $this->messageBus->dispatch(message: $command, stamps: $stamps);

            return null;
        }

        return $this->handle(
            message: count(value: $stamps) > 0 ? Envelope::wrap(message: $command, stamps: $stamps) : $command
        );
    }
}
