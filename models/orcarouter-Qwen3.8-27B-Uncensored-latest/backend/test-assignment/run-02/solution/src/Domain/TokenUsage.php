<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Кол-во израсходованных токенов по видам (целые >= 0).
 */
final class TokenUsage
{
    /** Порядок видов токенов и их ключи в справочнике цен. */
    public const KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $v) {
            if ($v < 0) {
                throw new \InvalidArgumentException('Token counts must be >= 0');
            }
        }
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }

    /** @return array<string, int> ключи из KINDS => количество */
    public function byKind(): array
    {
        return [
            'input' => $this->input,
            'output' => $this->output,
            'cache_read' => $this->cacheRead,
            'cache_write' => $this->cacheWrite,
        ];
    }
}
