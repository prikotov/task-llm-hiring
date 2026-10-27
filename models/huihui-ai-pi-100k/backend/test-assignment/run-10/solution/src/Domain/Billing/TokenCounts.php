<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Количество токенов по видам. Возвратные поля, неизменяемый объект.
 */
final class TokenCounts
{
    /**
     * Имена видов токенов, как в справочнике цен.
     *
     * @return string[]
     */
    public const KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }

    /**
     * Возврат по ключу вида токена (input, output, cache_read, cache_write).
     *
     * @throws \DomainException если вид неизвестен
     */
    public function kind(string $kind): int
    {
        return match ($kind) {
            'input' => $this->input,
            'output' => $this->output,
            'cache_read' => $this->cacheRead,
            'cache_write' => $this->cacheWrite,
            default => throw new \DomainException('Unknown token kind: ' . $kind),
        };
    }

    public static function fromArray(array $arr): self
    {
        return new self(
            input: (int) ($arr['input'] ?? 0),
            output: (int) ($arr['output'] ?? 0),
            cacheRead: (int) ($arr['cache_read'] ?? 0),
            cacheWrite: (int) ($arr['cache_write'] ?? 0),
        );
    }
}
