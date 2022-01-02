<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Exception;

use Dinecat\Cqrs\Command\CommandInterface;
use Dinecat\Cqrs\Exception\CommandValidationErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function sprintf;

/**
 * @coversDefaultClass \Dinecat\Cqrs\Exception\CommandValidationErrorException
 *
 * @internal
 */
final class CommandValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method returns right configured instance and has right parent class.
     *
     * @covers ::byCommand
     */
    public function testByCommandMethod(): void
    {
        $command = $this->createMock(CommandInterface::class);

        $exception = CommandValidationErrorException::byCommand($command);

        self::assertEquals(
            sprintf('Command "%s" validation error (missed middleware or validation rules).', $command::class),
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
        $command = new class() implements CommandInterface {
            public function getSomething(): int
            {
                return 42;
            }
        };

        $exception = CommandValidationErrorException::byProperty($command, 'something');

        self::assertEquals(
            sprintf(
                'Property "something" with value "42" in command "%s" failed validation (misconfigured validation rules?).',
                $command::class
            ),
            $exception->getMessage()
        );
    }
}
