<?php

declare(strict_types=1);

namespace Dinecat\Messenger\Exception;

use Dinecat\Messenger\QueryBus\QueryMessageInterface;
use InvalidArgumentException;
use function get_class;

/**
 * Exception for cases when invalid query passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class QueryValidationErrorException extends InvalidArgumentException
{
    public static function byQuery(QueryMessageInterface $query): self
    {
        return new self(sprintf(
            'Query "%s" validation error (missed middleware or validation rules).',
            get_class($query)
        ));
    }
}
