<?php

declare(strict_types=1);

namespace App\Billing\Engine;

use App\Billing\Dto\TokenCounts;
use App\Billing\Store\Store;
use App\Domain\Billing\Money;
use App\Domain\Billing\OffPeakWindow;
use App\Billing\Entity\PriceRow as PriceRowEntity;
use App\Domain\Billing\PricingScheme;

/**
 * Стоимость одного события:
 *   base   = Σ (tokens_вид × rate_вид) / 1 000 000
 *            или (Σ tokens) × rate_any / 1 000 000 (unified);
 *   итого  = base × multiplier_if_off_peak.
 *
 * Все промежуточные шаги — bcmath; округление ровно одно — итог до 6 (HALF_UP).
 */
final class EventCostCalculator
{
    public function __construct(
        private readonly Store $store,
    ) {}

    /** Вычисляет неокруглённую базовую стоимость (без оф-пика). */
    public function baseCost(TokenCounts $tokens, PriceRowEntity $row): Money
    {
        $divisor = Money::from('1000000');
        $total = Money::zero();

        try {
            $scheme = PricingScheme::parse($row->unit);
        } catch (\InvalidArgumentException) {
            throw new \RuntimeException(sprintf('Unknown pricing unit "%s".', $row->unit), 0, null);
        }

        switch ($scheme) {
            case PricingScheme::PER_TOKEN:
                $pairs = [
                    'input' => $tokens->input,
                    'output' => $tokens->output,
                    'cache_read' => $tokens->cacheRead,
                    'cache_write' => $tokens->cacheWrite,
                ];
                foreach ($pairs as $kind => $count) {
                    if ($count === 0) {
                        continue;
                    }
                    $rate = $row->rates[$kind] ?? null;
                    if ($rate === null) {
                        throw new \RuntimeException(sprintf('Missing rate for kind "%s" (unit=%s).', $kind, $row->unit));
                    }
                    $total = $total->add(
                        Money::from((string) $count)
                            ->multiplyBy(Money::from($rate))
                            ->divideBy($divisor)
                    );
                }
                break;

            case PricingScheme::PER_TOKEN_UNIFIED:
                $any = $row->rates['any'] ?? null;
                if ($any === null) {
                    throw new \RuntimeException('Unit per_1m_tokens_unified requires rate "any".');
                }
                $all = $tokens->total();
                if ($all === 0) {
                    return Money::zero();
                }
                $total = Money::from((string) $all)
                    ->multiplyBy(Money::from($any))
                    ->divideBy($divisor);
                break;
        }

        return $total;
    }

    /**
     * Итог с учётом оф-пика события и единственное допустимое округление.
     */
    public function computeEventCost(string $providerCode, \DateTimeImmutable $tsUtc, TokenCounts $tokens, PriceRowEntity $row): Money
    {
        $cost = $this->baseCost($tokens, $row);

        $offPeak = $this->store->providerOffPeak($providerCode);
        if ($offPeak !== null && $offPeak->contains(self::minutesOfDay($tsUtc))) {
            $cost = $cost->multiplyBy(Money::from($offPeak->multiplier()));
        }

        return $cost->roundToMicros();
    }

    public static function minutesOfDay(\DateTimeImmutable $tsUtc): int
    {
        return (int) $tsUtc->format('H') * 60 + (int) $tsUtc->format('i');
    }
}
