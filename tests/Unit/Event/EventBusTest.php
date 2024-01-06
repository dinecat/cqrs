<?php

declare(strict_types=1);

namespace Dinecat\CQRSTests\Unit\Event;

use Dinecat\CQRS\Event\EventBus;
use Dinecat\CQRS\Event\EventInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @coversDefaultClass \Dinecat\CQRS\Event\EventBus
 *
 * @internal
 */
final class EventBusTest extends TestCase
{
    /**
     * Checks is event dispatched.
     *
     * @covers ::__construct
     * @covers ::event
     */
    public function testOnExecuteCommand(): void
    {
        (new EventBus(eventMessageBus: $this->getMessageBusMock()))
            ->event(event: $this->createMock(originalClassName: EventInterface::class));
    }

    private function getMessageBusMock(): MessageBusInterface
    {
        $messageBus = $this->createMock(originalClassName: MessageBusInterface::class);

        $messageBus
            ->expects(self::once())
            ->method(constraint: 'dispatch')
            ->willReturnCallback(callback: static fn (EventInterface $event) => new Envelope(message: $event));

        return $messageBus;
    }
}
