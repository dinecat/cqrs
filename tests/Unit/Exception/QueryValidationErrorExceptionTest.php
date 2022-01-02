<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Exception;

use Dinecat\Cqrs\Exception\QueryValidationErrorException;
use Dinecat\Cqrs\Query\QueryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function sprintf;

/**
 * @coversDefaultClass \Dinecat\Cqrs\Exception\QueryValidationErrorException
 *
 * @internal
 */
final class QueryValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method return right configured instance and right parent.
     *
     * @covers ::byQuery
     */
    public function testByQueryCreation(): void
    {
        $query = $this->createMock(QueryInterface::class);

        $exception = QueryValidationErrorException::byQuery($query);

        self::assertEquals(
            sprintf('Query "%s" validation error (missed middleware or validation rules).', $query::class),
            $exception->getMessage()
        );

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    /**
     * Checks if method returns right configured instance.
     *
     * @covers ::byProperty
     */
    public function testByPropertyMethod(): void
    {
        $query = new class() implements QueryInterface {
            public function getSomething(): int
            {
                return 42;
            }
        };

        $exception = QueryValidationErrorException::byProperty($query, 'something');

        self::assertEquals(
            sprintf(
                'Property "something" with value "42" in query "%s" failed validation (misconfigured validation rules?).',
                $query::class
            ),
            $exception->getMessage()
        );
    }
}
