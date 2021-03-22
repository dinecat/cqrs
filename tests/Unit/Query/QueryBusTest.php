<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Query;

use Dinecat\Cqrs\Query\QueryBus;
use Dinecat\Cqrs\Query\QueryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @covers \Dinecat\Cqrs\Query\QueryBus
 *
 * @internal
 */
final class QueryBusTest extends TestCase
{
    /**
     * Checks is query dispatched and result is returned.
     *
     * @covers \Dinecat\Cqrs\Query\QueryBus::query
     */
    public function testOnPerformQuery(): void
    {
        $bus = new QueryBus($this->getMessageBusMock());

        $result = $bus->query($this->createMock(QueryInterface::class));

        self::assertEquals(['something'], $result);
    }

    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $envelope = $this->createMock(Envelope::class);
        $handledStamp = $this->createMock(HandledStamp::class);

        $handledStamp->method('getResult')->willReturn(['something']);

        $envelope->method('all')->willReturn([$handledStamp]);

        $messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(fn (QueryInterface $query) => $envelope);

        return $messageBus;
    }
}
