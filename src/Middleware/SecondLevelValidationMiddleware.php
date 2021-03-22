<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

/**
 * Middleware can be used for expense validation rules, apply it after and only if first validation layer passed.
 * Can be useful in some cases. For move rule to second layer you need just set his group to "PostValidation".
 */
final class SecondLevelValidationMiddleware implements MiddlewareInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $violations = $this->validator->validate($message, null, ['PostValidation']);

        if (count($violations)) {
            throw new ValidationFailedException($message, $violations);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
