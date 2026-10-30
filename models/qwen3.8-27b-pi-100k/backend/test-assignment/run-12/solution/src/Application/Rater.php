<?php

declare(strict_types=1);

namespace App\Application;

use App\Entity\PriceRow;
use App\Entity\UsageEventRow;
use App\ValueObject\Money;

/**
 * Ядро тарификации события (bcmath, масштаб 40, без float).
 *
 * Возвращает точную стоимость события, с учётом off-peak окна провайдера.
 * HALF_UP до 6 знаков — на стороне вызывающего (Rate / Report / Reprice).
 */
final class Rater
{
    public const SCALE = 40;

    /** @param array<string, array{name:string, start:string|null, end:string|null, multiplier:string|null}> $providers */
    public function __construct(private readonly array $providers)
    {
    }

    /**
     * Полная стоимость события (строка bcmath).
     */
    public function cost(UsageEventRow $event, PriceRow $priceRow): string
    {
        $total = '0';

        if ($priceRow->unit === 'per_1m_tokens_unified') {
            $rate = $priceRow->rateAny ?? '0';
            foreach ([
                (string) $event->tokensInput,
                (string) $event->tokensOutput,
                (string) $event->tokensCacheRead,
                (string) $event->tokensCacheWrite,
            ] as $qty) {
                $total = bcadd($total, bcmul($qty, $rate, self::SCALE), self::SCALE);
            }
        } else {
            foreach ([
                [(string) $event->tokensInput, $priceRow->rateInput ?? '0'],
                [(string) $event->tokensOutput, $priceRow->rateOutput ?? '0'],
                [(string) $event->tokensCacheRead, $priceRow->rateCacheRead ?? '0'],
                [(string) $event->tokensCacheWrite, $priceRow->rateCacheWrite ?? '0'],
            ] as [$qty, $rate]) {
                $total = bcadd($total, bcmul($qty, $rate, self::SCALE), self::SCALE);
            }
        }

        $total = bcdiv($total, '1000000', self::SCALE);

        return $this->applyOffPeak($event, $total);
    }

    /** Округлённая до 6 знаков стоимость. */
    public function costRounded(UsageEventRow $event, PriceRow $priceRow): string
    {
        return Money::of($this->cost($event, $priceRow))->roundHalfUpTo6()->toFixed6();
    }

    private function applyOffPeak(UsageEventRow $event, string $cost): string
    {
        $p = $this->providers[$event->provider] ?? null;
        if ($p === null || $p['start'] === null || $p['end'] === null || $p['multiplier'] === null) {
            return $cost;
        }

        $minuteOfDay = (int) $event->ts->format('H') * 60 + (int) $event->ts->format('i');
        $start = self::parseHM($p['start']);
        $end = self::parseHM($p['end']);

        if ($start <= $end) {
            $inWindow = ($minuteOfDay >= $start) && ($minuteOfDay < $end);
        } else {
            // пересечение полночи
            $inWindow = ($minuteOfDay >= $start) || ($minuteOfDay < $end);
        }

        return $inWindow ? bcmul($cost, $p['multiplier'], self::SCALE) : $cost;
    }

    private static function parseHM(string $hm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hm, 2));

        return $h * 60 + $m;
    }
}
