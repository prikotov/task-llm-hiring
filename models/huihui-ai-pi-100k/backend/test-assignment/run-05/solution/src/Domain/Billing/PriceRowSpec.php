<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Строка цен: схема тарификации и ставки (десятичные строки).
 */
final class PriceRowSpec
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';

    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    private function __construct(
        public readonly string $unit,
        /** @var array<string, string> ставки: десятичные строки, не float */
        public readonly array $rates,
    ) {
    }

    public static function perToken(string $input, string $output, string $cacheRead, string $cacheWrite): self
    {
        return new self(self::UNIT_PER_TOKEN, [
            'input' => $input,
            'output' => $output,
            'cache_read' => $cacheRead,
            'cache_write' => $cacheWrite,
        ]);
    }

    public static function unified(string $any): self
    {
        return new self(self::UNIT_UNIFIED, ['any' => $any]);
    }

    public function isPerToken(): bool
    {
        return $this->unit === self::UNIT_PER_TOKEN;
    }

    public function isUnified(): bool
    {
        return $this->unit === self::UNIT_UNIFIED;
    }

    /** Ставка по ключу вида токена ('input'|'output'|'cache_read'|'cache_write'|'any'). */
    public function rate(string $key): string
    {
        return $this->rates[$key] ?? '0';
    }
}
