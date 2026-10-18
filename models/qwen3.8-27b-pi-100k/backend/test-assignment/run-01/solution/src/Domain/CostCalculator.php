<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

/**
 * Расчёт стоимости одного события использования.
 *
 * Все промежуточные значения — точные (bcmath, масштаб {@see Money::SCALE});
 * округление происходит ровно один раз — итоговая стоимость события, HALF_UP
 * до 6 знаков. Чистые функции без доступа к БД — удобно для юнит-тестов.
 */
final class CostCalculator
{
    /** Схема: цена за 1M токенов отдельно по каждому виду. */
    public const UNIT_PER_KIND = 'per_1m_tokens';

    /** Схема: единая цена за 1M токенов любого вида. */
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    public const TOKENS_PER_UNIT = '1000000';

    private const PER_KIND_RATES = ['input', 'output', 'cache_read', 'cache_write'];

    /**
     * @param array<string, string> $rates ставки строки цен (десятичные строки)
     */
    public static function eventCost(
        string $unit,
        array $rates,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        ?OffPeakWindow $offPeak,
        int $minuteOfDayUtc,
    ): string {
        self::assertKnownUnit($unit);

        $base = match ($unit) {
            self::UNIT_PER_KIND => self::costByKind($rates, $tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite),
            self::UNIT_UNIFIED => self::costUnified($rates, $tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite),
        };

        if ($offPeak !== null && $offPeak->containsMinute($minuteOfDayUtc)) {
            $base = Money::mul($base, $offPeak->multiplier());
        }

        return Money::roundHalfUp($base);
    }

    private static function assertKnownUnit(string $unit): void
    {
        if ($unit !== self::UNIT_PER_KIND && $unit !== self::UNIT_UNIFIED) {
            throw new DomainException(sprintf('Неизвестная схема тарификации: %s.', $unit));
        }
    }

    /** cost = Σ tokens_вид × rate_вид / 1 000 000 */
    /**
     * @param array<string, string> $rates
     */
    private static function costByKind(array $rates, int $in, int $out, int $cr, int $cw): string
    {
        $tokens = [
            'input' => $in,
            'output' => $out,
            'cache_read' => $cr,
            'cache_write' => $cw,
        ];

        $sum = '0';
        foreach (self::PER_KIND_RATES as $kind) {
            if (!isset($rates[$kind])) {
                throw new DomainException(sprintf('Для схемы %s отсутствует ставка вида «%s».', self::UNIT_PER_KIND, $kind));
            }

            $sum = Money::add($sum, Money::mul((string) $tokens[$kind], $rates[$kind]));
        }

        return Money::div($sum, self::TOKENS_PER_UNIT);
    }

    /** cost = (Σ всех видов токенов) × rate_any / 1 000 000 */
    /**
     * @param array<string, string> $rates
     */
    private static function costUnified(array $rates, int $in, int $out, int $cr, int $cw): string
    {
        if (!isset($rates['any'])) {
            throw new DomainException('Для схемы per_1m_tokens_unified отсутствует ставка «any».');
        }

        $total = Money::add(Money::add((string) $in, (string) $out), Money::add((string) $cr, (string) $cw));

        return Money::div(Money::mul($total, $rates['any']), self::TOKENS_PER_UNIT);
    }

    /** Минуты суток UTC (0–1439) из момента времени. */
    public static function minuteOfDayUtc(\DateTimeImmutable $tsUtc): int
    {
        $dt = $tsUtc->setTimezone(new \DateTimeZone('UTC'));

        return ((int) $dt->format('G')) * 60 + (int) $dt->format('i');
    }
}
