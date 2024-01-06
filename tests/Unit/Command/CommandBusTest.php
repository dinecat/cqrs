<?php

declare(strict_types=1);

namespace Dinecat\CQRSTests\Unit\Command;

use Dinecat\CQRS\Command\AsyncCommandInterface;
use Dinecat\CQRS\Command\CommandBus;
use Dinecat\CQRS\Command\CommandInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @coversDefaultClass \Dinecat\CQRS\Command\CommandBus
 *
 * @internal
 */
final class CommandBusTest extends TestCase
{
    /**
     * Checks are command dispatched, and the result is returned. Also, check if HandleTrait works as expected.
     *
     * @covers ::__construct
     * @covers ::command
     */
    public function testOnExecuteCommand(): void
    {
        $bus = new CommandBus(commandMessageBus: $this->getMessageBusMock());

        $result = $bus->command(command: $this->createMock(originalClassName: CommandInterface::class));

        self::assertEquals(expected: 42, actual: $result);
    }

    /**
     * Checks the async command is dispatched, and null is returned.
     *
     * @covers ::__construct
     * @covers ::command
     */
    public function testOnExecuteAsyncCommand(): void
    {
        $bus = new CommandBus(commandMessageBus: $this->getMessageBusMock());

        self::assertNull(actual: $bus->command(command: $this->createMock(
            originalClassName: AsyncCommandInterface::class
        )));
    }

    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(originalClassName: MessageBusInterface::class);

        $messageBus
            ->expects(self::once())
            ->method(constraint: 'dispatch')
            ->willReturnCallback(callback: static fn (CommandInterface $command) => new Envelope(
                message: $command,
                stamps: [new HandledStamp(result: 42, handlerName: 'some')]
            ));

        return $messageBus;
    }
}
