<?php

declare(strict_types=1);

namespace Dinecat\Cqrs\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;
use function is_array;

/**
 * Middleware can be useful for expensive validation rules.
 *
 * Validation is applied after and only if first validation layer passed. You can specify validation groups, which
 * should be executed at second layer. For execute rule at second layer you need to add his to one of specified groups,
 * by default "L2Validation".
 */
final class SecondLayerValidationMiddleware implements MiddlewareInterface
{
    /**
     * @var array<string>
     */
    private array $validationGroups;

    /**
     * @param array<string>|string $validationGroups
     */
    public function __construct(private ValidatorInterface $validator, array|string $validationGroups = 'L2Validation')
    {
        $this->validationGroups = is_array($validationGroups) ? $validationGroups : [$validationGroups];
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $violations = $this->validator->validate($message, null, $this->validationGroups);

        if (count($violations) > 0) {
            throw new ValidationFailedException($message, $violations);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
