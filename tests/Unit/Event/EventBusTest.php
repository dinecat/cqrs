<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Event;

use Dinecat\Cqrs\Event\EventBus;
use Dinecat\Cqrs\Event\EventInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @coversDefaultClass \Dinecat\Cqrs\Event\EventBus
 *
 * @internal
 */
final class EventBusTest extends TestCase
{
    /**
     * Checks is event dispatched.
     *
     * @covers ::event
     */
    public function testOnExecuteCommand(): void
    {
        $bus = new EventBus($this->getMessageBusMock());

        $bus->event($this->createMock(EventInterface::class));
    }

    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(MessageBusInterface::class);

        $messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(fn (EventInterface $event) => new Envelope($event));

        return $messageBus;
    }
}
