<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Exception;

use Dinecat\Cqrs\Command\CommandInterface;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function sprintf;
use function var_export;

/**
 * Exception for cases when invalid command passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class CommandValidationErrorException extends InvalidArgumentException
{
    public static function byCommand(CommandInterface $command): self
    {
        return new self(sprintf(
            'Command "%s" validation error (missed middleware or validation rules).',
            $command::class
        ));
    }

    public static function byProperty(CommandInterface $command, string $propertyName): self
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return new self(sprintf(
            'Property "%s" with value "%s" in command "%s" failed validation (misconfigured validation rules?).',
            $propertyName,
            var_export($propertyAccessor->getValue($command, $propertyName), true),
            $command::class
        ));
    }
}
