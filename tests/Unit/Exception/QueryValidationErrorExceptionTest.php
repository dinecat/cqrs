<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Exception;

use Dinecat\Cqrs\Exception\QueryValidationErrorException;
use Dinecat\Cqrs\Query\QueryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function get_class;
use function sprintf;

/**
 * @covers \Dinecat\Cqrs\Exception\QueryValidationErrorException
 *
 * @internal
 */
final class QueryValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method return right configured instance and right parent.
     *
     * @covers \Dinecat\Cqrs\Exception\QueryValidationErrorException::byQuery
     */
    public function testByQueryCreation(): void
    {
        $query = $this->createMock(QueryInterface::class);

        $exception = QueryValidationErrorException::byQuery($query);

        self::assertEquals(
            sprintf('Query "%s" validation error (missed middleware or validation rules).', get_class($query)),
            $exception->getMessage()
        );

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
    }
}
