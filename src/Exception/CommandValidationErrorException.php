<?php

declare(strict_types=1);

namespace Dinecat\Messenger\Exception;

use Dinecat\Messenger\CommandBus\CommandMessageInterface;
use InvalidArgumentException;
use function get_class;

/**
 * Exception for cases when invalid command passes validation level and enters the handler.
 *
 * @author Mykola Zyk <mykola.zyk@dinecat.com>
 */
final class CommandValidationErrorException extends InvalidArgumentException
{
    public static function byCommand(CommandMessageInterface $command): self
    {
        return new self(\sprintf(
            'Command "%s" validation error (missed middleware or validation rules).',
            get_class($command)
        ));
    }
}
