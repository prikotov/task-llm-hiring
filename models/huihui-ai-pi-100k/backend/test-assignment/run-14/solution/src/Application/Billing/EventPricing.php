<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\OffPeakWindow;
use App\Domain\UsageTokens;
use App\Entity\Price;

/**
 * Расчёт стоимости одного события по строке цен и правилу оф-пика.
 *
 * Чистая функция (без БД) — легко покрыть юнит-тестами.
 *
 * Все промежуточные вычисления — bcmath со scale = 16 (запас точности).
 * Округление — единственное: итог события до 6 знаков, HALF_UP.
 *
 * @internal только внутри пакета app
 */
final class EventPricing
{
    /** Запас точности промежуточных вычислений. */
    private const SCALE = 16;
    /** Денестр. */
    private const ONE_M = '1000000';
    private const INVERSE_ONE_M = '0.000001';

    public function price(UsageTokens $tokens, ?Price $price, ?OffPeakWindow $offPeak): string
    {
        if ($price === null) {
            throw new \DomainException('Нет строки цены для события.');
        }

        $cost = match ($price->getPricingUnit()->name) {
            'PerToken' => $this->perToken($tokens, $price),
            'Unified'  => $this->unified($tokens, $price),
        };

        if ($offPeak !== null) {
            $cost = bcmul($cost, $offPeak->getMultiplier(), self::SCALE);
        }

        // Один раз — на уровне события. HALF_UP до 6 знаков.
        return $this->roundHalfUp6($cost);
    }

    private function perToken(UsageTokens $t, Price $p): string
    {
        $parts = [
            bcmul(bcmul((string) $t->input, $p->rate('input'), self::SCALE), self::INVERSE_ONE_M, self::SCALE),
            bcmul(bcmul((string) $t->output, $p->rate('output'), self::SCALE), self::INVERSE_ONE_M, self::SCALE),
            bcmul(bcmul((string) $t->cacheRead, $p->rate('cache_read'), self::SCALE), self::INVERSE_ONE_M, self::SCALE),
            bcmul(bcmul((string) $t->cacheWrite, $p->rate('cache_write'), self::SCALE), self::INVERSE_ONE_M, self::SCALE),
        ];

        $sum = '0';
        foreach ($parts as $part) {
            $sum = bcadd($sum, $part, self::SCALE);
        }

        return $sum;
    }

    private function unified(UsageTokens $t, Price $p): string
    {
        $total = $t->total();

        return bcmul(
            bcmul((string) $total, $p->rate('any'), self::SCALE),
            '0.000001',
            self::SCALE,
        );
    }

    /**
     * HALF_UP-округление до 6 знаков: "2.5" → 3 (по правилу в задании).
     *
     * bcmath по умолчанию отбрасывает (trunc); HALF_UP =  trunc + 0.5*10^-6
     * для неотрицательных значений (цены у нас неотрицательные — токены ≥ 0
     * и ставки ≥ 0).
     */
    private function roundHalfUp6(string $x): string
    {
        if (bccomp($x, '0', self::SCALE) < 0) {
            return $x === '0' ? '0.000000' : '-'.self::roundHalfUp6(ltrim($x, '-'));
        }

        $neg = str_starts_with($x, '-');
        $abs = $neg ? substr($x, 1) : $x;

        // HALF_UP: дробная часть ≥ 0.5 → +1 в 6-м знаке; иначе отбрасываем.
        $shifted = bcmul($abs, (string) 10 ** 6, self::SCALE);
        $int = (string) (int) $shifted;
        $frac = bcsub($shifted, $int, self::SCALE);
        $up = bccomp($frac, '0.5', self::SCALE) >= 0 ? 1 : 0;
        $sum = bcadd($int, (string) $up, 0);

        $result = bcdiv($sum, (string) 10 ** 6, 6);
        return $neg ? '-'.$result : $result;
    }

    public static function minutesOfDayUtc(\DateTimeInterface $ts): int
    {
        /*
         * ts приходит в UTC (ISO-8601 "Z"). Формат ISO-8601 с "Z"
         * не несёт часового пояzona локально — но при парсинге PHP
         * даёт объект с timezone 'UTC' (offset 0), так что формат
         * 'H:i' возвращает часы UTC. Если ts пришёл в другом tz —
         * правило задания: всегда UTC.
         *
         * Для устойчивости переводим явно в UTC.
         */
        $utc = \DateTimeImmutable::createFromInterface($ts)->setTimezone(new \DateTimeZone('UTC'));

        return (int) $utc->format('G') * 60 + (int) $utc->format('i');
    }
}
