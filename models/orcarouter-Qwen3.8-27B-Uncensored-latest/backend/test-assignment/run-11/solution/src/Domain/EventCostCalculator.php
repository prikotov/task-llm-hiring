<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Расчёт стоимости одного события по строке цены.
 * Промежуточные значения — без округления (bcmath, масштаб Money::SCALE).
 * Финальная стоимость события — HALF_UP до 6 знаков.
 */
final class EventCostCalculator
{
    private const ONE_MILLION = 1_000_000;

    public static function oneMillion(): Money
    {
        return Money::fromString((string) self::ONE_MILLION);
    }

    /**
     * Точная (неокруглённая) стоимость события до оф-пик.
     *
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     */
    public function baseCost(PriceRow $row, array $tokens): Money
    {
        return match ($row->getUnit()) {
            PriceRow::UNIT_PER_1M => $this->perToken($row, $tokens),
            PriceRow::UNIT_PER_1M_UNIFIED => $this->unified($row, $tokens),
            default => throw new \DomainException('Unknown unit: '.$row->getUnit()),
        };
    }

    /**
     * Итоговая стоимость события: базовая стоимость × множитель оф-пика
     * (если ts в окне) → финальное округление HALF_UP до 6 знаков.
     *
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     */
    public function finalCost(
        PriceRow $row,
        Provider $provider,
        \DateTimeImmutable $utcTs,
        array $tokens,
    ): Money {
        $base = $this->baseCost($row, $tokens);

        if ($provider->hasOffPeak() && $provider->isInOffPeak($utcTs)) {
            $multiplier = $provider->offPeakMultiplier();
            if ($multiplier !== null) {
                $base = $base->multiply($multiplier);
            }
        }

        return Money::fromString($base->roundedToMicros());
    }

    /**
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     */
    private function perToken(PriceRow $row, array $tokens): Money
    {
        $sum = Money::zero();
        foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
            $rate = $row->rate($kind);
            if ($rate === null || $tokens[$kind] === 0) {
                continue;
            }
            $sum = $sum->add(
                Money::fromString((string) $tokens[$kind])
                    ->multiply($rate)
                    ->divide(self::oneMillion())
            );
        }

        return $sum;
    }

    /**
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     */
    private function unified(PriceRow $row, array $tokens): Money
    {
        $rate = $row->rate('any');
        if ($rate === null) {
            throw new \DomainException('Unified price row missing "any" rate');
        }

        $totalTokens = $tokens['input'] + $tokens['output']
            + $tokens['cache_read'] + $tokens['cache_write'];

        return Money::fromString((string) $totalTokens)
            ->multiply($rate)
            ->divide(self::oneMillion());
    }
}
