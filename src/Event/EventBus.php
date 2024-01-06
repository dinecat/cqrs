<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Event;

use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Bus for event messages.
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

    public function event(EventInterface $event): void
    {
        $this->messageBus->dispatch(message: $event);
    }
}
