<?php

declare(strict_types=1);

namespace Dinecat\MessengerTests\Unit\EventBus;

use Dinecat\Messenger\EventBus\EventBus;
use Dinecat\Messenger\EventBus\EventMessageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBusTest extends TestCase
{
    /**
     * Checks is event dispatched.
     */
    public function testOnExecuteCommand(): void
    {
        $bus = new EventBus($this->getMessageBusMock());

        $bus->event($this->getEventMessageMock());
    }

    /**
     * @return EventMessageInterface|MockObject
     */
    private function getEventMessageMock(): EventMessageInterface
    {
        return $this->createMock(EventMessageInterface::class);
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
            ->willReturn(new Envelope($this->getEventMessageMock()));

        return $messageBus;
    }
}
