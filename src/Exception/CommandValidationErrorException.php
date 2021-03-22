<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Exception;

use Dinecat\Cqrs\Command\CommandInterface;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use function get_class;
use function sprintf;

/**
 * Exception for cases when invalid command passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class CommandValidationErrorException extends InvalidArgumentException
{
    #[Pure]
    public static function byCommand(CommandInterface $command): self
    {
        return new self(sprintf(
            'Command "%s" validation error (missed middleware or validation rules).',
            get_class($command)
        ));
    }
}
