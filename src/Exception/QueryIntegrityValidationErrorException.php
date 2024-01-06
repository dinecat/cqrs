<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Exception;

use Dinecat\CQRS\Query\QueryInterface;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function gettype;
use function is_object;
use function sprintf;
use function str_replace;

/**
 * Exception for cases when invalid query passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class QueryIntegrityValidationErrorException extends InvalidArgumentException
{
    /**
     * It can be used for generic and specific errors with complex validation rules.
     */
    public static function general(QueryInterface $query, ?string $customMessage = null): self
    {
        return new self(sprintf(
            'Query "%s" integrity validation error%s (missed middleware or validation rules).',
            $query::class,
            $customMessage !== null ? sprintf(': "%s"', $customMessage) : ''
        ));
    }

    /**
     * Covers cases with missed or inaccessible property in query.
     */
    public static function propertyMissedOrInaccessible(object $query, string $propertyName): self
    {
        return new self(sprintf(
            'Property "%s" in query "%s" is required but is missed or inaccessible.',
            $propertyName,
            $query::class
        ));
    }

    /**
     * Covers cases with missed property value or invalid property type in query.
     *
     * @param class-string|string $type
     */
    public static function propertyHasInvalidValue(object $query, string $propertyName, string $type): self
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if (!$propertyAccessor->isReadable(objectOrArray: $query, propertyPath: $propertyName)) {
            return self::propertyMissedOrInaccessible(query: $query, propertyName: $propertyName);
        }

        $value = $propertyAccessor->getValue(objectOrArray: $query, propertyPath: $propertyName);

        return new self(sprintf(
            'Property "%s" in query "%s" has a value of invalid type (%s required but %s given).',
            $propertyName,
            $query::class,
            $type,
            is_object($value)
                ? $value::class
                : str_replace(search: 'double', replace: 'float', subject: gettype(value: $value))
        ));
    }
}
