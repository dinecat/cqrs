<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Query;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * Bus for query messages.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class QueryBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryMessageBus)
    {
        $this->messageBus = $queryMessageBus;
    }

    /**
     * Performs query and returns result (for synchronous queries).
     *
     * @param array<StampInterface> $stamps
     */
    public function query(QueryInterface $query, array $stamps = []): mixed
    {
        if ($query instanceof AsyncQueryInterface) {
            $this->messageBus->dispatch(message: $query, stamps: $stamps);

            return null;
        }

        return $this->handle(message: $query);
    }
}
