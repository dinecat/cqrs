<?php

declare(strict_types=1);

namespace Dinecat\CQRSTests\Unit\Exception;

use Dinecat\CQRS\Command\CommandInterface;
use Dinecat\CQRS\Exception\CommandIntegrityValidationErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * @coversDefaultClass \Dinecat\CQRS\Exception\CommandIntegrityValidationErrorException
 *
 * @internal
 */
final class CommandIntegrityValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method returns right configured instance and has right parent class.
     *
     * @covers ::general
     */
    public function testGeneralMethod(): void
    {
        $command = $this->createMock(originalClassName: CommandInterface::class);

        $exception = CommandIntegrityValidationErrorException::general(command: $command);

        self::assertEquals(
            expected: sprintf(
                'Command "%s" integrity validation error (missed middleware or validation rules).',
                $command::class
            ),
            actual: $exception->getMessage()
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        $exception = CommandIntegrityValidationErrorException::general(command: $command, customMessage: 'lorem ipsum');

        self::assertEquals(
            expected: sprintf(
                'Command "%s" integrity validation error: "lorem ipsum" (missed middleware or validation rules).',
                $command::class
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
        $command = $this->createMock(originalClassName: CommandInterface::class);

        $exception = CommandIntegrityValidationErrorException::propertyMissedOrInaccessible(
            command: $command,
            propertyName: 'someProperty'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            expected: sprintf(
                'Property "someProperty" in command "%s" is required but is missed or inaccessible.',
                $command::class
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
        $command = new class() implements CommandInterface {
            public function getNumber(): int
            {
                return 42;
            }

            public function getDecimal(): float
            {
                return 42.222;
            }

            public function getObject(): TestClass
            {
                return new TestClass();
            }
        };

        // nonexistent property
        $exception = CommandIntegrityValidationErrorException::propertyHasInvalidValue(
            command: $command,
            propertyName: 'nonexistent',
            type: 'string'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "nonexistent" in command "%s" is required but is missed or inaccessible.',
                $command::class
            ),
            $exception->getMessage()
        );

        // number
        $exception = CommandIntegrityValidationErrorException::propertyHasInvalidValue(
            command: $command,
            propertyName: 'number',
            type: 'string'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "number" in command "%s" has a value of invalid type (string required but integer given).',
                $command::class
            ),
            $exception->getMessage()
        );

        // decimal
        $exception = CommandIntegrityValidationErrorException::propertyHasInvalidValue(
            command: $command,
            propertyName: 'decimal',
            type: 'array'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "decimal" in command "%s" has a value of invalid type (array required but float given).',
                $command::class
            ),
            $exception->getMessage()
        );

        // object
        $exception = CommandIntegrityValidationErrorException::propertyHasInvalidValue(
            command: $command,
            propertyName: 'object',
            type: 'string'
        );

        self::assertInstanceOf(expected: InvalidArgumentException::class, actual: $exception);

        self::assertEquals(
            sprintf(
                'Property "object" in command "%s" has a value of invalid type (string required but %s given).',
                $command::class,
                TestClass::class
            ),
            $exception->getMessage()
        );
    }
}

class TestClass {}
