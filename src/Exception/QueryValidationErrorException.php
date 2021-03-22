<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Exception;

use Dinecat\Cqrs\Query\QueryInterface;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use function get_class;
use function sprintf;

/**
 * Exception for cases when invalid query passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class QueryValidationErrorException extends InvalidArgumentException
{
    #[Pure]
    public static function byQuery(QueryInterface $query): self
    {
        return new self(sprintf(
            'Query "%s" validation error (missed middleware or validation rules).',
            get_class($query)
        ));
    }
}
