<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Exception;

use Dinecat\Cqrs\Query\QueryInterface;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function sprintf;
use function var_export;

/**
 * Exception for cases when invalid query passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class QueryValidationErrorException extends InvalidArgumentException
{
    public static function byQuery(QueryInterface $query): self
    {
        return new self(sprintf(
            'Query "%s" validation error (missed middleware or validation rules).',
            $query::class
        ));
    }

    public static function byProperty(QueryInterface $query, string $propertyName): self
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return new self(sprintf(
            'Property "%s" with value "%s" in query "%s" failed validation (misconfigured validation rules?).',
            $propertyName,
            var_export($propertyAccessor->getValue($query, $propertyName), true),
            $query::class
        ));
    }
}
