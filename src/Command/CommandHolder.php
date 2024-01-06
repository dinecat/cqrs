<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Command;

use BackedEnum;
use Dinecat\CQRS\Exception\CommandIntegrityValidationErrorException;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use function is_a;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

/**
 * The decorator for command provides various access methods for command properties the handler requires.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 *
 * @template C of CommandInterface
 */
final class CommandHolder
{
    private ?PropertyAccessor $propertyAccessor = null;

    /**
     * @param C $command
     */
    public function __construct(private readonly CommandInterface $command) {}

    /**
     * @return C
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    /**
     * Returns an array value from the command property or throws a CommandValidationErrorException
     * when such property contains null or the value does not represent the array.
     *
     * @return array<mixed>
     */
    public function getArrayValue(string $propertyName): array
    {
        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (!is_array($value)) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: 'array'
            );
        }

        return $value;
    }

    /**
     * Returns a boolean value from the command property or throws a CommandValidationErrorException
     * when such property contains null or the value does not represent the boolean.
     */
    public function getBoolValue(string $propertyName): bool
    {
        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (!is_bool($value)) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: 'boolean'
            );
        }

        return $value;
    }

    /**
     * Returns enum from string/int value representation for property $propertyName from command and throws
     * CommandValidationErrorException when such property contains null or value does not represent any enum case.
     *
     * @template E of BackedEnum
     *
     * @param class-string<E> $enumClass
     *
     * @return E
     */
    public function getEnumValue(string $propertyName, string $enumClass): BackedEnum
    {
        if (!is_a(object_or_class: $enumClass, class: BackedEnum::class, allow_string: true)) {
            throw new InvalidArgumentException(sprintf(
                'Enum must be an instance of BackedEnum, %s given.',
                $enumClass
            ));
        }

        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (is_string(value: $enumClass::cases()[0]->value)) {
            if (!is_string(value: $value)) {
                throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                    command: $this->command,
                    propertyName: $propertyName,
                    type: 'string'
                );
            }
        } elseif (!is_int(value: $value)) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: 'integer'
            );
        }

        return $enumClass::tryFrom(value: $value) ?? throw CommandIntegrityValidationErrorException::general(
            command: $this->command,
            customMessage: sprintf('property %s for enum %s has an invalid value', $propertyName, $enumClass)
        );
    }

    /**
     * Returns a value as a floating point number from the command property or throws a CommandValidationErrorException
     * when such property contains null or the value does not represent the floating point number.
     */
    public function getFloatValue(string $propertyName): float
    {
        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (!is_float($value)) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: 'float'
            );
        }

        return $value;
    }

    /**
     * Returns an integer value from the command property or throws a CommandValidationErrorException
     * when such property contains null or the value does not represent the integer.
     */
    public function getIntValue(string $propertyName): int
    {
        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (!is_int($value)) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: 'integer'
            );
        }

        return $value;
    }

    /**
     * Returns a value as an instance of $valueClass from the command property
     * or throws a CommandValidationErrorException when such property contains null
     * or the value does not an instance of $valueClass.
     *
     * @template O of object
     *
     * @param class-string<O> $valueClass
     *
     * @return O
     */
    public function getObjectValue(string $propertyName, string $valueClass): object
    {
        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (!$value instanceof $valueClass) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: $valueClass
            );
        }

        return $value;
    }

    /**
     * Returns a string value from the command property or throws a CommandValidationErrorException
     * when such property contains null or the value does not represent the string.
     */
    public function getStringValue(string $propertyName): string
    {
        $value = $this->getPropertyValue(propertyName: $propertyName);

        if (!is_string($value)) {
            throw CommandIntegrityValidationErrorException::propertyHasInvalidValue(
                command: $this->command,
                propertyName: $propertyName,
                type: 'string'
            );
        }

        return $value;
    }

    private function getPropertyValue(string $propertyName): mixed
    {
        try {
            return $this->getPropertyAccessor()->getValue(objectOrArray: $this->command, propertyPath: $propertyName);
        } catch (NoSuchPropertyException) {
            throw CommandIntegrityValidationErrorException::propertyMissedOrInaccessible(
                command: $this->command,
                propertyName: $propertyName
            );
        }
    }

    private function getPropertyAccessor(): PropertyAccessor
    {
        if ($this->propertyAccessor === null) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
