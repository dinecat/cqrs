<?php

declare(strict_types=1);

namespace Dinecat\MessengerTests\Unit\Exception;

use Dinecat\Messenger\Exception\QueryValidationErrorException;
use Dinecat\Messenger\QueryBus\QueryMessageInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function get_class;
use function sprintf;

class QueryValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method return right configured instance and right parent.
     */
    public function testByQueryCreation(): void
    {
        $query = $this->getQueryMessageMock();

        $exception = QueryValidationErrorException::byQuery($query);

        $this->assertEquals(
            sprintf(
                'Query "%s" validation error (missed middleware or validation rules).',
                get_class($query)
            ),
            $exception->getMessage()
        );

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    /**
     * @return QueryMessageInterface|MockObject
     */
    private function getQueryMessageMock(): QueryMessageInterface
    {
        return $this->createMock(QueryMessageInterface::class);
    }
}
