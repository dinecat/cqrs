<?php

declare(strict_types=1);

namespace Dinecat\MessengerTests\Unit\CommandBus;

use Dinecat\Messenger\CommandBus\CommandBus;
use Dinecat\Messenger\CommandBus\CommandMessageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBusTest extends TestCase
{
    /**
     * Checks is command dispatched and result is returned.
     * Also checks if HandleTrait works as expected.
     */
    public function testOnExecuteCommand(): void
    {
        $bus = new CommandBus($this->getMessageBusMock());

        $result = $bus->command($this->getCommandMessageMock());

        $this->assertEquals(42, $result);
    }

    /**
     * @return CommandMessageInterface|MockObject
     */
    private function getCommandMessageMock(): CommandMessageInterface
    {
        return $this->createMock(CommandMessageInterface::class);
    }

    /**
     * @return MessageBusInterface|MockObject
     */
    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(MessageBusInterface::class);

        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope($this->getCommandMessageMock(), [new HandledStamp(42, '')]));

        return $messageBus;
    }
}
