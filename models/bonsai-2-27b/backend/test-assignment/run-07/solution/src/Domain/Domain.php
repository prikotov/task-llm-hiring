<?php

declare(strict_types=1);

namespace App\Domain;

use App\Entity\PriceRow;
use App\Entity\UsageEvent;

/**
 * Чистый расчёт стоимости события.
 *
 * Точная десятичная арифметика — только bcmath, без float.
 * Масштаб промежуточных вычислений — 20 знаков (запас).
 * Итог события — округление HALF_UP до 6 знаков (одна точка округления).
 */
final class Domain
{
    public const SCALE = 20;
    public const MONEY_SCALE = 6;
    public const TOKENS_PER_M = '1000000';

    /**
     * Стоимость события с учётом схемы тарификации и оф-пик.
     * Возвращает строку с точностью 6 знаков (округление HALF_UP).
     */
    public static function calculateCost(UsageEvent $event, PriceRow $priceRow, array $providerOffPeak): string
    {
        $cost = match ($priceRow->getUnit()) {
            'per_1m_tokens' => self::calculatePerToken($event, $priceRow),
            'per_1m_tokens_unified' => self::calculateUnified($event, $priceRow),
            default => throw new \InvalidArgumentException('Неизвестная unit: ' . $priceRow->getUnit()),
        };

        if ($providerOffPeak !== [] && self::inOffPeak($event, $providerOffPeak)) {
            $multiplier = (string) ($providerOffPeak['multiplier'] ?? '1.000000');
            $cost = bcmul($cost, $multiplier, self::SCALE);
        }

        return self::roundHalfUp($cost, self::MONEY_SCALE);
    }

    private static function calculatePerToken(UsageEvent $event, PriceRow $priceRow): string
    {
        $sum = '0.00000000000000000000';
        $sum = bcadd($sum, self::tokensTimesRate($event->getTokensInput(), $priceRow->getRateInput() ?? '0.000000'), self::SCALE);
        $sum = bcadd($sum, self::tokensTimesRate($event->getTokensOutput(), $priceRow->getRateOutput() ?? '0.000000'), self::SCALE);
        $sum = bcadd($sum, self::tokensTimesRate($event->getTokensCacheRead(), $priceRow->getRateCacheRead() ?? '0.000000'), self::SCALE);
        $sum = bcadd($sum, self::tokensTimesRate($event->getTokensCacheWrite(), $priceRow->getRateCacheWrite() ?? '0.000000'), self::SCALE);
        return $sum;
    }

    private static function calculateUnified(UsageEvent $event, PriceRow $priceRow): string
    {
        $total = $event->getTokensInput() + $event->getTokensOutput() + $event->getTokensCacheRead() + $event->getTokensCacheWrite();
        return self::tokensTimesRate($total, $priceRow->getRateAny() ?? '0.000000');
    }

    /**
     * tokens * rate / 1_000_000 — точная десятичная арифметика.
     */
    private static function tokensTimesRate(int $tokens, string $rate): string
    {
        $product = bcmul((string) $tokens, $rate, self::SCALE);
        return bcdiv($product, self::TOKENS_PER_M, self::SCALE);
    }

    /**
     * @param array<string,string> $offPeak
     */
    private static function inOffPeak(UsageEvent $event, array $offPeak): bool
    {
        $start = (string) ($offPeak['window_start'] ?? '');
        $end = (string) ($offPeak['window_end'] ?? '');
        return self::timeInWindow($event->getTs(), $start, $end);
    }

    /**
     * Время суток $ts в окне [start, end). Окно может пересекать полночь.
     */
    private static function timeInWindow(\DateTimeImmutable $ts, string $start, string $end): bool
    {
        $minutes = self::toMinutes($ts);
        $startMinutes = self::parseHHMM($start);
        $endMinutes = self::parseHHMM($end);
        if ($startMinutes === null || $endMinutes === null) {
            return false;
        }
        if ($startMinutes < $endMinutes) {
            return $startMinutes <= $minutes && $minutes < $endMinutes;
        }

        return $minutes >= $startMinutes || $minutes < $endMinutes;
    }

    private static function toMinutes(\DateTimeImmutable $ts): int
    {
        return $ts->format('H') * 60 + (int) $ts->format('i');
    }

    /**
     * @return int|null минуты в [0; 1439]
     */
    private static function parseHHMM(string $h): ?int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $h, $m)) {
            return null;
        }
        $hours = (int) $m[1];
        $minutes = (int) $m[2];
        if ($hours < 0 || $hours >= 24 || $minutes < 0 || $minutes >= 60) {
            return null;
        }
        return $hours * 60 + $minutes;
    }

    /**
     * bcmath_pow10($scale) = "1" . $scale zeros.
     */
    private static function pow10(int $scale): string
    {
        return '1' . str_repeat('0', $scale);
    }

    /**
     * Округление HALF_UP до $scale знаков.
     *
     * bcmath не имеет прямого HALF_UP. Схема:
     * value * 10^scale  ->  сохранённая дробная часть (scale знаков).
     * + 0.5, затем отбрасываем дробную часть (floor), делим обратно.
     */
    public static function roundHalfUp(string $value, int $scale): string
    {
        $shift = self::pow10($scale);
        $scaled = bcmul($value, $shift, self::MONEY_SCALE);
        $scaled = bcadd($scaled, '0.5', self::MONEY_SCALE);
        $int = bcdiv($scaled, '1', 0); // floor: отбрасываем дробную часть
        return bcdiv($int, $shift, $scale);
    }

    /**
     * Сложение двух money-строк (6 знаков).
     */
    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::MONEY_SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::MONEY_SCALE);
    }
}
