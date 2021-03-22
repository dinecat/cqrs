<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Query;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Query message bus.
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
     * Performs query and returns result.
     */
    public function query(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }
}
