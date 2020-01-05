<?php

declare(strict_types=1);

namespace Dinecat\MessengerTests\Unit\Middleware;

use Dinecat\Messenger\CommandBus\CommandMessageInterface;
use Dinecat\Messenger\Middleware\SecondLevelValidationMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecondValidationMiddlewareTest extends TestCase
{
    /**
     * Checks if valid message can go through second validation middleware.
     */
    public function testHandleValidMessage(): void
    {
        $message = $this->getMessageMock();
        $envelope = new Envelope($message);

        $middleware = new SecondLevelValidationMiddleware($this->getValidatorForValidMessageMock($message));

        $middleware->handle($envelope, $this->getStackForValidMessageMock($envelope));
    }

    /**
     * Checks if invalid message raise exception.
     */
    public function testHandleInvalidMessage(): void
    {
        $message = $this->getMessageMock();
        $envelope = new Envelope($message);

        $this->expectException(ValidationFailedException::class);

        $middleware = new SecondLevelValidationMiddleware($this->getValidatorForInvalidMessageMock($message));

        $middleware->handle($envelope, $this->getStackForInvalidMessageMock($envelope));
    }

    /**
     * @return CommandMessageInterface|MockObject
     */
    private function getMessageMock(): CommandMessageInterface
    {
        return $this->createMock(CommandMessageInterface::class);
    }

    /**
     * @return StackMiddleware|MockObject
     */
    private function getStackForValidMessageMock(Envelope $envelope): StackMiddleware
    {
        $stack = $this->createMock(StackMiddleware::class);

        $stack->expects($this->once())->method('next')->willReturnSelf();
        $stack->expects($this->once())->method('handle')->with($envelope, $stack)->willReturn($envelope);

        return $stack;
    }

    /**
     * @return StackMiddleware|MockObject
     */
    private function getStackForInvalidMessageMock(Envelope $envelope): StackMiddleware
    {
        $stack = $this->createMock(StackMiddleware::class);

        $stack->expects($this->never())->method('next')->willReturnSelf();
        $stack->expects($this->never())->method('handle')->with($envelope, $stack)->willReturn($envelope);

        return $stack;
    }

    /**
     * @return ValidatorInterface|MockObject
     */
    private function getValidatorForValidMessageMock(CommandMessageInterface $message): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($message, null, ['PostValidation'])
            ->willReturn([]);

        return $validator;
    }

    /**
     * @return ValidatorInterface|MockObject
     */
    private function getValidatorForInvalidMessageMock(CommandMessageInterface $message): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($message, null, ['PostValidation'])
            ->willReturn($violationList);

        return $validator;
    }
}
