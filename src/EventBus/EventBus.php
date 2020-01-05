<?php

declare(strict_types=1);

namespace Dinecat\Messenger\EventBus;

use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Decorator for event message bus.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class EventBus
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $eventMessageBus)
    {
        $this->messageBus = $eventMessageBus;
    }

    public function event(EventMessageInterface $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
