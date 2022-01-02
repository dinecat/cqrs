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
 * @coversDefaultClass \Dinecat\Cqrs\Query\QueryBus
 *
 * @internal
 */
final class QueryBusTest extends TestCase
{
    /**
     * Checks is query dispatched and result is returned.
     *
     * @covers ::query
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

        $messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                fn (QueryInterface $query) => new Envelope($query, [new HandledStamp(['something'], 'some')])
            );

        return $messageBus;
    }
}
