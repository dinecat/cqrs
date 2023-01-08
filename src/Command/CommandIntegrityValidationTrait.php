<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Command;

use BackedEnum;
use Dinecat\Cqrs\Exception\CommandValidationErrorException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Helper for command integrity validation.
 *
 * Covers the cases when command validation rules for required properties missed or not work by some reason, and we have
 * command with required non-nullable property and null value in command handler.
 */
trait CommandIntegrityValidationTrait
{
    /**
     * Returns value for property $propertyName from command, throws CommandValidationErrorException
     * when such property contains null.
     */
    private function requirePropertyValue(CommandInterface $command, string $propertyName): mixed
    {
        return PropertyAccess::createPropertyAccessor()->getValue($command, $propertyName)
            ?? throw CommandValidationErrorException::byProperty($command, $propertyName);
    }

    /**
     * Returns enum value from string/int representation for property $propertyName from command, throws
     * CommandValidationErrorException when such property contains null or value not represent any enum case.
     *
     * @param class-string<BackedEnum> $enumClass
     */
    private function requirePropertyAsEnum(CommandInterface $command, string $propertyName, string $enumClass): mixed
    {
        $backedValue = PropertyAccess::createPropertyAccessor()->getValue($command, $propertyName)
            ?? throw CommandValidationErrorException::byProperty($command, $propertyName);

        return $enumClass::tryFrom($backedValue)
            ?? throw CommandValidationErrorException::byProperty($command, $propertyName);
    }
}
