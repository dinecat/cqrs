<?php

declare(strict_types=1);

namespace Dinecat\MessengerTests\Unit\QueryBus;

use Dinecat\Messenger\QueryBus\QueryBus;
use Dinecat\Messenger\QueryBus\QueryMessageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBusTest extends TestCase
{
    /**
     * Checks is query dispatched and result is returned.
     */
    public function testOnPerformQuery(): void
    {
        $bus = new QueryBus($this->getMessageBusMock());

        $result = $bus->query($this->getQueryMessageMock());

        $this->assertEquals(['something'], $result);
    }

    /**
     * @return QueryMessageInterface|MockObject
     */
    private function getQueryMessageMock(): QueryMessageInterface
    {
        return $this->createMock(QueryMessageInterface::class);
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
            ->willReturn(new Envelope($this->getQueryMessageMock(), [new HandledStamp(['something'], '')]));

        return $messageBus;
    }
}
