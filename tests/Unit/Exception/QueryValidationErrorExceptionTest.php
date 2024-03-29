<?php

declare(strict_types=1);

namespace Dinecat\CQRSTests\Unit\Exception;

use Dinecat\CQRS\Exception\QueryIntegrityValidationErrorException;
use Dinecat\CQRS\Query\QueryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * @coversDefaultClass \Dinecat\CQRS\Exception\QueryIntegrityValidationErrorException
 *
 * @internal
 */
final class QueryValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method returns right configured instance and has right parent class.
     *
     * @covers ::general
     */
    public function testGeneralMethod(): void
    {
        $query = $this->createMock(originalClassName: QueryInterface::class);

        $exception = QueryIntegrityValidationErrorException::general(query: $query);

        self::assertEquals(
            expected: sprintf(
                'Query "%s" integrity validation error (missed middleware or validation rules).',
                $query::class
            ),
            actual: $exception->getMessage()
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        $exception = QueryIntegrityValidationErrorException::general(query: $query, customMessage: 'lorem ipsum');

        self::assertEquals(
            expected: sprintf(
                'Query "%s" integrity validation error: "lorem ipsum" (missed middleware or validation rules).',
                $query::class
            ),
            actual: $exception->getMessage()
        );
    }

    /**
     * Checks if method returns right configured instance and has right parent class.
     *
     * @covers ::propertyMissedOrInaccessible
     */
    public function testPropertyMissedOrInaccessibleMethod(): void
    {
        $query = $this->createMock(originalClassName: QueryInterface::class);

        $exception = QueryIntegrityValidationErrorException::propertyMissedOrInaccessible(
            query: $query,
            propertyName: 'someProperty'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            expected: sprintf(
                'Property "someProperty" in query "%s" is required but is missed or inaccessible.',
                $query::class
            ),
            actual: $exception->getMessage()
        );
    }

    /**
     * Checks if method returns right configured instance and has right parent class.
     *
     * @covers ::propertyHasInvalidValue
     */
    public function testPropertyHasInvalidValueMethod(): void
    {
        $query = new class() implements QueryInterface {
            public function getNumber(): int
            {
                return 42;
            }

            public function getDecimal(): float
            {
                return 42.222;
            }

            public function getObject(): Test2Class
            {
                return new Test2Class();
            }
        };

        // nonexistent property
        $exception = QueryIntegrityValidationErrorException::propertyHasInvalidValue(
            query: $query,
            propertyName: 'nonexistent',
            type: 'string'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "nonexistent" in query "%s" is required but is missed or inaccessible.',
                $query::class
            ),
            $exception->getMessage()
        );

        // number
        $exception = QueryIntegrityValidationErrorException::propertyHasInvalidValue(
            query: $query,
            propertyName: 'number',
            type: 'string'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "number" in query "%s" has a value of invalid type (string required but integer given).',
                $query::class
            ),
            $exception->getMessage()
        );

        // decimal
        $exception = QueryIntegrityValidationErrorException::propertyHasInvalidValue(
            query: $query,
            propertyName: 'decimal',
            type: 'array'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "decimal" in query "%s" has a value of invalid type (array required but float given).',
                $query::class
            ),
            $exception->getMessage()
        );

        // object
        $exception = QueryIntegrityValidationErrorException::propertyHasInvalidValue(
            query: $query,
            propertyName: 'object',
            type: 'string'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "object" in query "%s" has a value of invalid type (string required but %s given).',
                $query::class,
                Test2Class::class
            ),
            $exception->getMessage()
        );
    }
}

class Test2Class {}
