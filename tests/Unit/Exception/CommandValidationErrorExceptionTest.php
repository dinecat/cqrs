<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Exception;

use Dinecat\Cqrs\Command\CommandInterface;
use Dinecat\Cqrs\Exception\CommandValidationErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function get_class;
use function sprintf;

/**
 * @covers \Dinecat\Cqrs\Exception\CommandValidationErrorException
 *
 * @internal
 */
final class CommandValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method return right configured instance and right parent.
     *
     * @covers \Dinecat\Cqrs\Exception\CommandValidationErrorException::byCommand
     */
    public function testByCommandCreation(): void
    {
        $command = $this->createMock(CommandInterface::class);

        $exception = CommandValidationErrorException::byCommand($command);

        self::assertEquals(
            sprintf('Command "%s" validation error (missed middleware or validation rules).', get_class($command)),
            $exception->getMessage()
        );

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
    }
}
