<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Decimal;
use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use DateTimeImmutable;

/**
 * Расчёт стоимости одного события (чистая функция, без инфраструктуры).
 *
 * Правила:
 *  - per_1m_tokens:        cost = Σ tokens_вид × rate_вид / 1_000_000
 *  - per_1m_tokens_unified: cost = (всех токенов) × rate_any / 1_000_000
 *  - оф-пик: стоимость *целиком* события × multiplier, если ts в окне;
 *  - промежуточные вычисления — без округления (bcmath, масштаб 20);
 *  - ровно одно округление: итог события до 6 знаков HALF_UP.
 */
final class RatesCalculator
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    /** Стоимость события до применения оф-пик, без округления. */
    public function baseCost(TokenUsage $usage, string $unit, array $rates): string
    {
        return match ($unit) {
            self::UNIT_PER_TOKEN => $this->costByKind($usage, $rates),
            self::UNIT_UNIFIED => Decimal::div(
                Decimal::mul((string) $usage->total(), $rates['any']),
                '1000000',
            ),
            default => throw new \InvalidArgumentException(sprintf('unknown unit "%s"', $unit)),
        };
    }

    /** Итоговая стоимость события: оф-пик + единственное округление до 6 знаков. */
    public function finalCost(
        TokenUsage $usage,
        string $unit,
        array $rates,
        ?OffPeakWindow $window,
        DateTimeImmutable $tsUtc,
    ): string {
        $cost = $this->baseCost($usage, $unit, $rates);

        if ($window !== null) {
            $cost = $window->apply($cost, $tsUtc);
        }

        return Decimal::roundHalfUp($cost, 6);
    }

    private function costByKind(TokenUsage $usage, array $rates): string
    {
        $total = '0';
        foreach ($usage->byKind() as $kind => $count) {
            $total = Decimal::add(
                $total,
                Decimal::div(Decimal::mul((string) $count, $rates[$kind]), '1000000'),
            );
        }

        return $total;
    }
}
