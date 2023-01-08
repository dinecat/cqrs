<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Query;

use Dinecat\Cqrs\Exception\QueryValidationErrorException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Helper for query integrity validation.
 *
 * Covers the cases when query validation rules for required properties missed or not work by some reason, and we have
 * query with required non-nullable property and null value in query handler.
 */
trait QueryIntegrityValidationTrait
{
    /**
     * Returns value for property $propertyName from query, throws QueryValidationErrorException
     * when such property contains null.
     */
    private function requirePropertyValue(QueryInterface $query, string $propertyName): mixed
    {
        return PropertyAccess::createPropertyAccessor()->getValue($query, $propertyName)
            ?? throw QueryValidationErrorException::byProperty($query, $propertyName);
    }
}
