<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Middleware;

use Dinecat\Cqrs\Command\CommandInterface;
use Dinecat\Cqrs\Middleware\SecondLayerValidationMiddleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \Dinecat\Cqrs\Middleware\SecondLayerValidationMiddleware
 *
 * @internal
 */
final class SecondLayerValidationMiddlewareTest extends TestCase
{
    /**
     * Checks if valid message can go through second validation middleware.
     *
     * @covers ::handle
     */
    public function testHandleValidMessage(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $envelope = new Envelope($command);

        $middleware = new SecondLayerValidationMiddleware($this->getValidatorForMessageMock($command, 0));

        $middleware->handle($envelope, $this->getStackForValidMessageMock($envelope));
    }

    /**
     * Checks if invalid message raise exception.
     *
     * @covers ::handle
     */
    public function testHandleInvalidMessage(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $envelope = new Envelope($command);

        $this->expectException(ValidationFailedException::class);

        $middleware = new SecondLayerValidationMiddleware($this->getValidatorForMessageMock($command, 1));

        $middleware->handle($envelope, $this->getStackForInvalidMessageMock($envelope));
    }

    private function getStackForValidMessageMock(Envelope $envelope): StackMiddleware
    {
        $stack = $this->createMock(StackMiddleware::class);

        $stack->expects(self::once())->method('next')->willReturnSelf();
        $stack->expects(self::once())->method('handle')->with($envelope, $stack)->willReturn($envelope);

        return $stack;
    }

    private function getStackForInvalidMessageMock(Envelope $envelope): StackMiddleware
    {
        $stack = $this->createMock(StackMiddleware::class);

        $stack->expects(self::never())->method('next')->willReturnSelf();
        $stack->expects(self::never())->method('handle')->with($envelope, $stack)->willReturn($envelope);

        return $stack;
    }

    private function getValidatorForMessageMock(CommandInterface $message, int $violationsCount): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects(self::once())
            ->method('count')
            ->willReturn($violationsCount);

        $validator
            ->expects(self::once())
            ->method('validate')
            ->with($message, null, ['L2Validation'])
            ->willReturn($violationList);

        return $validator;
    }
}
