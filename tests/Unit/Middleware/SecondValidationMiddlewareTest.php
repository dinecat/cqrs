<?php

declare(strict_types=1);

namespace Dinecat\CqrsTests\Unit\Middleware;

use Dinecat\Cqrs\Command\CommandInterface;
use Dinecat\Cqrs\Middleware\SecondLevelValidationMiddleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Dinecat\Cqrs\Middleware\SecondLevelValidationMiddleware
 *
 * @internal
 */
final class SecondValidationMiddlewareTest extends TestCase
{
    /**
     * Checks if valid message can go through second validation middleware.
     *
     * @covers \Dinecat\Cqrs\Middleware\SecondLevelValidationMiddleware::handle
     */
    public function testHandleValidMessage(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $envelope = $this->createMock(Envelope::class);

        $envelope->expects(self::once())->method('getMessage')->willReturn($command);

        $middleware = new SecondLevelValidationMiddleware($this->getValidatorForValidMessageMock($command));

        $middleware->handle($envelope, $this->getStackForValidMessageMock($envelope));
    }

    /**
     * Checks if invalid message raise exception.
     *
     * @covers \Dinecat\Cqrs\Middleware\SecondLevelValidationMiddleware::handle
     */
    public function testHandleInvalidMessage(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $envelope = $this->createMock(Envelope::class);

        $envelope->expects(self::once())->method('getMessage')->willReturn($command);

        $this->expectException(ValidationFailedException::class);

        $middleware = new SecondLevelValidationMiddleware($this->getValidatorForInvalidMessageMock($command));

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

    private function getValidatorForValidMessageMock(CommandInterface $message): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $validator
            ->expects(self::once())
            ->method('validate')
            ->with($message, null, ['PostValidation'])
            ->willReturn([]);

        return $validator;
    }

    private function getValidatorForInvalidMessageMock(CommandInterface $message): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects(self::once())
            ->method('count')
            ->willReturn(1);

        $validator
            ->expects(self::once())
            ->method('validate')
            ->with($message, null, ['PostValidation'])
            ->willReturn($violationList);

        return $validator;
    }
}
