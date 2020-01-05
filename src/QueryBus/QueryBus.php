<?php

declare(strict_types=1);

namespace Dinecat\Messenger\QueryBus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Decorator for query message bus.
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
     * @return mixed The handler returned value
     */
    public function query(QueryMessageInterface $query)
    {
        return $this->handle($query);
    }
}
