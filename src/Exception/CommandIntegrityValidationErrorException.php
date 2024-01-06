<?php

declare(strict_types=1);

namespace Dinecat\CQRS\Exception;

use Dinecat\CQRS\Command\CommandInterface;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function gettype;
use function is_object;
use function sprintf;
use function str_replace;

/**
 * Exception for cases when invalid command passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class CommandIntegrityValidationErrorException extends InvalidArgumentException
{
    /**
     * It can be used for generic and specific errors with complex validation rules.
     */
    public static function general(CommandInterface $command, ?string $customMessage = null): self
    {
        return new self(sprintf(
            'Command "%s" integrity validation error%s (missed middleware or validation rules).',
            $command::class,
            $customMessage !== null ? sprintf(': "%s"', $customMessage) : ''
        ));
    }

    /**
     * Covers cases with missed or inaccessible property in command.
     */
    public static function propertyMissedOrInaccessible(object $command, string $propertyName): self
    {
        return new self(sprintf(
            'Property "%s" in command "%s" is required but is missed or inaccessible.',
            $propertyName,
            $command::class
        ));
    }

    /**
     * Covers cases with missed property value or invalid property type in command.
     *
     * @param class-string|string $type
     */
    public static function propertyHasInvalidValue(object $command, string $propertyName, string $type): self
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if (!$propertyAccessor->isReadable(objectOrArray: $command, propertyPath: $propertyName)) {
            return self::propertyMissedOrInaccessible(command: $command, propertyName: $propertyName);
        }

        $value = $propertyAccessor->getValue(objectOrArray: $command, propertyPath: $propertyName);

        return new self(sprintf(
            'Property "%s" in command "%s" has a value of invalid type (%s required but %s given).',
            $propertyName,
            $command::class,
            $type,
            is_object($value)
                ? $value::class
                : str_replace(search: 'double', replace: 'float', subject: gettype(value: $value))
        ));
    }
}
