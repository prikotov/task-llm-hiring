<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * Расход токенов одним событием, разбитый по видам.
 * Вида — фиксированный набор: input, output, cache_read, cache_write.
 */
final class TokenUsage
{
    public const KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    /** @var array<string, int> */
    private array $tokens;

    /** @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens */
    private function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /** @param array<string, int> $tokens */
    public static function fromKinds(array $tokens): self
    {
        foreach (self::KINDS as $kind) {
            if (!isset($tokens[$kind]) || !is_int($tokens[$kind]) || $tokens[$kind] < 0) {
                throw new \DomainException(sprintf('tokens_%s: требуется целое >= 0', $kind));
            }
        }

        return new self($tokens);
    }

    public function get(string $kind): int
    {
        return $this->tokens[$kind];
    }

    public function total(): int
    {
        return array_sum($this->tokens);
    }
}
