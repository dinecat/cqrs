<?php

declare(strict_types=1);

namespace Dinecat\CQRSTests\Unit\Command;

use Dinecat\CQRS\Command\CommandHolder;
use Dinecat\CQRS\Command\CommandInterface;
use Dinecat\CQRS\Exception\CommandIntegrityValidationErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * @coversDefaultClass \Dinecat\CQRS\Command\CommandHolder
 *
 * @internal
 */
final class CommandHolderTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCommand
     */
    public function testGetCommand(): void
    {
        $command = $this->createMock(originalClassName: CommandInterface::class);

        self::assertSame(expected: $command, actual: (new CommandHolder(command: $command))->getCommand());
    }

    /**
     * @dataProvider getInvalidCases
     *
     * @param class-string|string $type
     * @param class-string|null|string $required
     * @param class-string|null|string $given
     *
     * @covers ::getArrayValue
     * @covers ::getBoolValue
     * @covers ::getEnumValue
     * @covers ::getFloatValue
     * @covers ::getIntValue
     * @covers ::getObjectValue
     * @covers ::getPropertyAccessor
     * @covers ::getPropertyValue
     * @covers ::getStringValue
     */
    public function testInvalidProperty(
        string $propertyName,
        string $type,
        string $message,
        ?string $required = null,
        ?string $given = null
    ): void {
        $command = $this->buildCommand();

        $this->expectException(
            $type === 'unit-enum'
                ? InvalidArgumentException::class
                : CommandIntegrityValidationErrorException::class
        );
        $this->expectExceptionMessage(match ($message) {
            'missed' => sprintf(
                'Property "%s" in command "%s" is required but is missed or inaccessible.',
                $propertyName,
                $command::class
            ),
            'invalid' => sprintf(
                'Property "%s" in command "%s" has a value of invalid type (%s required but %s given).',
                $propertyName,
                $command::class,
                $required ?? $type,
                $given ?? 'string'
            ),
            'enum-invalid' => sprintf(
                'Command "%s" integrity validation error: "property %s for enum %s has an invalid value" (%s).',
                $command::class,
                $propertyName,
                $required ?? $type,
                'missed middleware or validation rules'
            ),
            'enum-class' => sprintf('Enum must be an instance of BackedEnum, %s given.', TestClass::class),
            default => ''
        });

        $commandHolder = new CommandHolder(command: $command);

        match ($type) {
            'array' => $commandHolder->getArrayValue(propertyName: $propertyName),
            'boolean' => $commandHolder->getBoolValue(propertyName: $propertyName),
            'unit-enum' => $commandHolder->getEnumValue(propertyName: $propertyName, enumClass: TestClass::class),
            'str-enum' => $commandHolder->getEnumValue(propertyName: $propertyName, enumClass: TestEnum::class),
            'int-enum' => $commandHolder->getEnumValue(propertyName: $propertyName, enumClass: TestIntEnum::class),
            'float' => $commandHolder->getFloatValue(propertyName: $propertyName),
            'integer' => $commandHolder->getIntValue(propertyName: $propertyName),
            'string' => $commandHolder->getStringValue(propertyName: $propertyName),
            default => $commandHolder->getObjectValue(propertyName: $propertyName, valueClass: $required ?? $type)
        };
    }

    /*public function testInvalidEnumType(): void
    {

    }*/

    /**
     * @covers ::getArrayValue
     * @covers ::getBoolValue
     * @covers ::getEnumValue
     * @covers ::getFloatValue
     * @covers ::getIntValue
     * @covers ::getObjectValue
     * @covers ::getPropertyAccessor
     * @covers ::getPropertyValue
     * @covers ::getStringValue
     */
    public function testValidProperty(): void
    {
        $commandHolder = new CommandHolder(command: $this->buildCommand());

        self::assertEquals(expected: ['something'], actual: $commandHolder->getArrayValue(propertyName: 'array'));
        self::assertFalse(condition: $commandHolder->getBoolValue(propertyName: 'boolean'));
        self::assertSame(
            expected: TestEnum::Two,
            actual: $commandHolder->getEnumValue(propertyName: 'enum', enumClass: TestEnum::class)
        );
        self::assertSame(
            expected: TestIntEnum::Two,
            actual: $commandHolder->getEnumValue(propertyName: 'enumInt', enumClass: TestIntEnum::class)
        );
        self::assertEquals(expected: 1.22, actual: $commandHolder->getFloatValue(propertyName: 'float'));
        self::assertEquals(expected: 42, actual: $commandHolder->getIntValue(propertyName: 'integer'));
        self::assertInstanceOf(
            expected: TestClass::class,
            actual: $commandHolder->getObjectValue(propertyName: 'object', valueClass: TestClass::class)
        );
        self::assertEquals(expected: 'something', actual: $commandHolder->getStringValue(propertyName: 'string'));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getInvalidCases(): array
    {
        return [
            'array_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'array', 'message' => 'missed'],
            'array_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'array', 'message' => 'missed'],
            'array_invalid_type_value' => ['propertyName' => 'string', 'type' => 'array', 'message' => 'invalid'],
            'bool_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'boolean', 'message' => 'missed'],
            'bool_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'boolean', 'message' => 'missed'],
            'bool_invalid_type_value' => ['propertyName' => 'string', 'type' => 'boolean', 'message' => 'invalid'],
            'enum_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'str-enum', 'message' => 'missed'],
            'enum_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'str-enum', 'message' => 'missed'],
            'enum_invalid_class' => ['propertyName' => 'enum', 'type' => 'unit-enum', 'message' => 'enum-class'],
            'enum_invalid_type_value' => [
                'propertyName' => 'array',
                'type' => 'str-enum',
                'message' => 'invalid',
                'required' => 'string',
                'given' => 'array',
            ],
            'enum_invalid_type_value_2' => [
                'propertyName' => 'enumInt',
                'type' => 'str-enum',
                'message' => 'invalid',
                'required' => 'string',
                'given' => 'integer',
            ],
            'enum_invalid_type_value_3' => [
                'propertyName' => 'enum',
                'type' => 'int-enum',
                'message' => 'invalid',
                'required' => 'integer',
                'given' => 'string',
            ],
            'enum_invalid_value' => [
                'propertyName' => 'string',
                'type' => 'str-enum',
                'message' => 'enum-invalid',
                'required' => TestEnum::class,
            ],
            'enum_invalid_value_2' => [
                'propertyName' => 'integer',
                'type' => 'int-enum',
                'message' => 'enum-invalid',
                'required' => TestIntEnum::class,
            ],
            'float_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'float', 'message' => 'missed'],
            'float_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'float', 'message' => 'missed'],
            'float_invalid_type_value' => ['propertyName' => 'string', 'type' => 'float', 'message' => 'invalid'],
            'int_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'integer', 'message' => 'missed'],
            'int_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'integer', 'message' => 'missed'],
            'int_invalid_type_value' => ['propertyName' => 'string', 'type' => 'integer', 'message' => 'invalid'],
            'object_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'object', 'message' => 'missed'],
            'object_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'object', 'message' => 'missed'],
            'object_invalid_type_value' => ['propertyName' => 'string', 'type' => 'object', 'message' => 'invalid'],
            'object_invalid_type_value_2' => [
                'propertyName' => 'object',
                'type' => 'object',
                'message' => 'invalid',
                'required' => TestEnum::class,
                'given' => TestClass::class,
            ],
            'string_missed_property' => ['propertyName' => 'nonexistent', 'type' => 'string', 'message' => 'missed'],
            'string_inaccessible_property' => ['propertyName' => 'noaccess', 'type' => 'string', 'message' => 'missed'],
            'string_invalid_type_value' => [
                'propertyName' => 'array',
                'type' => 'string',
                'message' => 'invalid',
                'required' => 'string',
                'given' => 'array',
            ],
        ];
    }

    private function buildCommand(): CommandInterface
    {
        return new class() implements CommandInterface {
            /**
             * @param array<string> $array
             */
            public function __construct(
                private readonly array $array = ['something'],
                private readonly bool $boolean = false,
                private readonly string $enum = 'two',
                private readonly int $enumInt = 2,
                private readonly float $float = 1.22,
                private readonly int $integer = 42,
                /* @noinspection PhpPropertyOnlyWrittenInspection UnknownInspection @phpstan-ignore-next-line */
                private readonly string $noaccess = 'something',
                private readonly TestClass $object = new TestClass(),
                private readonly string $string = 'something',
            ) {}

            /**
             * @return array<string>
             */
            public function getArray(): array
            {
                return $this->array;
            }

            public function isBoolean(): bool
            {
                return $this->boolean;
            }

            public function getEnum(): string
            {
                return $this->enum;
            }

            public function getEnumInt(): int
            {
                return $this->enumInt;
            }

            public function getFloat(): float
            {
                return $this->float;
            }

            public function getInteger(): int
            {
                return $this->integer;
            }

            public function getObject(): TestClass
            {
                return $this->object;
            }

            public function getString(): string
            {
                return $this->string;
            }
        };
    }
}

class TestClass {}

enum TestEnum: string
{
    case One = 'one';
    case Two = 'two';
}

enum TestIntEnum: int
{
    case One = 1;
    case Two = 2;
}
