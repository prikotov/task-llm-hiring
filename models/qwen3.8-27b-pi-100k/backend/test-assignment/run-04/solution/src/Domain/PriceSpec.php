<?php

declare(strict_types=1);

namespace App\Domain;

use App\Exception\ImportException;

/**
 * Строка цен: схема тарификации + ставки.
 *
 * @psalm-type Rates = array{input?: string, output?: string, cache_read?: string, cache_write?: string, any?: string}
 */
final readonly class PriceSpec
{
    public const UNIT_PER_1M = 'per_1m_tokens';

    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        public string $unit,
        public array $rates,
    ) {}

    /**
     * Строит и проверяет строку ставок по схеме (unit).
     *
     * @param array<string, mixed> $rates
     */
    public static function create(string $unit, array $rates, string $context = ''): self
    {
        $where = $context !== '' ? sprintf(' (%s)', $context) : '';

        if (!is_array($rates) || $rates === []) {
            throw new ImportException("rates{$where}: ожидается непустой объект со ставками");
        }

        $require = match ($unit) {
            self::UNIT_PER_1M => ['input', 'output', 'cache_read', 'cache_write'],
            self::UNIT_UNIFIED => ['any'],
            default => throw new ImportException(
                sprintf('unit%s: неизвестная схема тарификации "%s" (допустимы "%s", "%s")', $where, $unit, self::UNIT_PER_1M, self::UNIT_UNIFIED)
            ),
        };

        $unknown = array_diff(array_keys($rates), $require);
        if ($unknown !== []) {
            throw new ImportException(sprintf(
                'rates%s: ставки %s не относятся к схеме "%s"',
                $where,
                implode(', ', array_map(strval(...), $unknown)),
                $unit,
            ));
        }

        $clean = [];
        foreach ($require as $key) {
            $clean[$key] = self::validateRate($rates[$key] ?? null, $key, $where);
        }

        return new self($unit, $clean);
    }

    /**
     * Точная стоимость события без учёта оф-пика и финального округления.
     */
    public function baseCost(int $tokensInput, int $tokensOutput, int $tokensCacheRead, int $tokensCacheWrite): string
    {
        if ($this->unit === self::UNIT_UNIFIED) {
            $total = \bcadd(
                \bcadd((string) $tokensInput, (string) $tokensOutput),
                \bcadd((string) $tokensCacheRead, (string) $tokensCacheWrite),
            );

            return Money::tokensAtRate($total, $this->rates['any']);
        }

        return Money::add(
            Money::add(
                Money::tokensAtRate((string) $tokensInput, $this->rates['input']),
                Money::tokensAtRate((string) $tokensOutput, $this->rates['output']),
            ),
            Money::add(
                Money::tokensAtRate((string) $tokensCacheRead, $this->rates['cache_read']),
                Money::tokensAtRate((string) $tokensCacheWrite, $this->rates['cache_write']),
            ),
        );
    }

    /** @param mixed $value */
    private static function validateRate($value, string $key, string $where): string
    {
        // Ставки — строго строки: числа JSON запрещены (float/округление — источник ошибок).
        if (!\is_string($value)) {
            throw new ImportException(sprintf(
                'rates.%s%s: ставка должна быть десятичной строкой (числа JSON запрещены), получено %s',
                $key,
                $where,
                get_debug_type($value),
            ));
        }

        if (!preg_match('/^\d+(\.\d{1,6})?$/', $value)) {
            throw new ImportException(sprintf(
                'rates.%s%s: ожидается десятичная строка (например "3.000000"), получено "%s"',
                $key,
                $where,
                $value,
            ));
        }

        return $value;
    }
}
