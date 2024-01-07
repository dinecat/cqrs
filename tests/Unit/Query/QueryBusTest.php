<?php

declare(strict_types=1);

namespace Dinecat\CQRSTests\Unit\Query;

use Dinecat\CQRS\Query\AsyncQueryInterface;
use Dinecat\CQRS\Query\QueryBus;
use Dinecat\CQRS\Query\QueryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @coversDefaultClass \Dinecat\CQRS\Query\QueryBus
 *
 * @internal
 */
final class QueryBusTest extends TestCase
{
    /**
     * Checks the query is dispatched and result is returned.
     *
     * @covers ::__construct
     * @covers ::query
     */
    public function testOnPerformQuery(): void
    {
        self::assertEquals(
            expected: ['something'],
            actual: (new QueryBus(queryMessageBus: $this->getMessageBusMock()))
                ->query(query: $this->createMock(originalClassName: QueryInterface::class))
        );
    }

    /**
     * Checks the async query is dispatched, and null is returned.
     *
     * @covers ::__construct
     * @covers ::query
     */
    public function testOnPerformAsyncQuery(): void
    {
        self::assertNull(
            actual: (new QueryBus(queryMessageBus: $this->getMessageBusMock()))
                ->query(query: $this->createMock(originalClassName: AsyncQueryInterface::class))
        );
    }

    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(originalClassName: MessageBusInterface::class);

        $messageBus
            ->expects(self::once())
            ->method(constraint: 'dispatch')
            ->willReturnCallback(callback: static fn (QueryInterface $query) => new Envelope(
                message: $query,
                stamps: [new HandledStamp(result: ['something'], handlerName: 'some')]
            ));

        return $messageBus;
    }
}
