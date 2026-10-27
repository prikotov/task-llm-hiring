<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use DomainException;

/**
 * Чистая логика тарификации одного события (без ORM).
 *
 * Все промежуточные вычисления — bcmath без округления; единственное
 * округление — HALF_UP до 6 знаков итоговой стоимости события.
 */
final class Rater
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    private const DIVISOR = '1000000'; // цена — за 1 000 000 токенов

    /**
     * Стоимость события до единственного допустимого округления:
     * базовая стоимость по схеме тарификации, умноженная на оф-пик
     * (если время суток попадает в окно провайдера).
     *
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     */
    public static function cost(
        array $tokens,
        UnitRates $rates,
        ?OffPeakPolicy $offPeak,
        DateTimeImmutable $ts,
    ): string {
        $cost = self::baseCost($tokens, $rates);

        if ($offPeak !== null && $offPeak->covers($ts)) {
            $cost = bcmul($cost, $offPeak->multiplier(), Decimal::SCALE);
        }

        return Decimal::halfUp6($cost);
    }

    /**
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     */
    public static function baseCost(array $tokens, UnitRates $rates): string
    {
        if ($rates->unit === self::UNIT_PER_TOKEN) {
            $sum = '0';
            $sum = bcadd($sum, bcmul((string) $tokens['input'], (string) $rates->input, Decimal::SCALE), Decimal::SCALE);
            $sum = bcadd($sum, bcmul((string) $tokens['output'], (string) $rates->output, Decimal::SCALE), Decimal::SCALE);
            $sum = bcadd($sum, bcmul((string) $tokens['cache_read'], (string) $rates->cacheRead, Decimal::SCALE), Decimal::SCALE);
            $sum = bcadd($sum, bcmul((string) $tokens['cache_write'], (string) $rates->cacheWrite, Decimal::SCALE), Decimal::SCALE);
        } elseif ($rates->unit === self::UNIT_UNIFIED) {
            $total = $tokens['input'] + $tokens['output'] + $tokens['cache_read'] + $tokens['cache_write'];
            $sum = bcmul((string) $total, (string) $rates->any, Decimal::SCALE);
        } else {
            throw new DomainException(sprintf('Неизвестная схема тарификации: %s', $rates->unit));
        }

        return bcdiv($sum, self::DIVISOR, Decimal::SCALE + 2);
    }
}
