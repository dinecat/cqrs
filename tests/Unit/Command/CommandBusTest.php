<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Command;

use Dinecat\Cqrs\Command\CommandBus;
use Dinecat\Cqrs\Command\CommandInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @covers \Dinecat\Cqrs\Command\CommandBus
 *
 * @internal
 */
final class CommandBusTest extends TestCase
{
    /**
     * Checks is command dispatched and result is returned.
     * Also checks if HandleTrait works as expected.
     *
     * @covers \Dinecat\Cqrs\Command\CommandBus::command
     */
    public function testOnExecuteCommand(): void
    {
        $bus = new CommandBus($this->getMessageBusMock());

        $result = $bus->command($this->createMock(CommandInterface::class));

        self::assertEquals(42, $result);
    }

    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $envelope = $this->createMock(Envelope::class);
        $handledStamp = $this->createMock(HandledStamp::class);

        $handledStamp->method('getResult')->willReturn(42);

        $envelope->method('all')->willReturn([$handledStamp]);

        $messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(fn (CommandInterface $command) => $envelope);

        return $messageBus;
    }
}
