<?php

declare(strict_types=1);

namespace Dinecat\MessengerTests\Unit\Exception;

use Dinecat\Messenger\CommandBus\CommandMessageInterface;
use Dinecat\Messenger\Exception\CommandValidationErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function get_class;
use function sprintf;

class CommandValidationErrorExceptionTest extends TestCase
{
    /**
     * Checks if method return right configured instance and right parent.
     */
    public function testByCommandCreation(): void
    {
        $command = $this->getCommandMessageMock();

        $exception = CommandValidationErrorException::byCommand($command);

        $this->assertEquals(
            sprintf(
                'Command "%s" validation error (missed middleware or validation rules).',
                get_class($command)
            ),
            $exception->getMessage()
        );

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    /**
     * @return CommandMessageInterface|MockObject
     */
    private function getCommandMessageMock(): CommandMessageInterface
    {
        return $this->createMock(CommandMessageInterface::class);
    }
}
