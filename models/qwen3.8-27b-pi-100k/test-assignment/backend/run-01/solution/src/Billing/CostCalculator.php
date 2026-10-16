<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;
use App\Support\Decimals;

/**
 * Чистая функция тарификации одного события (без БД):
 * строка цены + токены + оф-пик провайдера + момент события → стоимость.
 *
 * Правила (см. TASK.md, «Правила тарификации»):
 *  - per_1m_tokens:         cost = Σ tokens_вид × rate_вид / 1e6;
 *  - per_1m_tokens_unified: cost = (Σ всех токенов) × rate_any / 1e6;
 *  - если ts (UTC) попадает в оф-пик окно провайдера [start, end) —
 *    стоимость всего события умножается на multiplier;
 *  - промежуточные расчёты — bcmath (масштаб 20), без округления;
 *  - ровно одно округление: итог события — HALF_UP до 6 знаков.
 */
final class CostCalculator
{
    private const TOKENS_PER_MILLION = '1000000';

    /**
     * Точная (без округления) стоимость события до оф-пик, decimal-строка.
     *
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     */
    public function baseCost(PriceRow $priceRow, array $tokens): string
    {
        if ($priceRow->getUnit() === PriceRow::UNIT_PER_TOKEN) {
            $sum = '0';
            foreach (PriceRow::PER_TOKEN_RATE_KEYS as $key) {
                $sum = Decimals::add($sum, $this->piece($priceRow->getRates()[$key], $tokens[$key]));
            }

            return $sum;
        }

        if ($priceRow->getUnit() === PriceRow::UNIT_PER_TOKEN_UNIFIED) {
            return $this->piece($priceRow->getRates()[PriceRow::UNIFIED_RATE_KEY], array_sum($tokens));
        }

        throw new \DomainException(sprintf('Неизвестная схема тарификации: "%s"', $priceRow->getUnit()));
    }

    /**
     * Полная стоимость события: базовая стоимость + оф-пик,
     * с единственным допустимым округлением (HALF_UP до 6 знаков).
     *
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     * @return array{baseCost:string, costUsd:string, offPeakApplied:bool}
     */
    public function calculate(
        PriceRow $priceRow,
        array $tokens,
        ?OffPeak $offPeak,
        \DateTimeImmutable $utcTs,
    ): array {
        $baseCost = $this->baseCost($priceRow, $tokens);

        $offPeakApplied = $offPeak !== null && $offPeak->contains($utcTs);
        if ($offPeakApplied) {
            $baseCost = Decimals::mul($baseCost, $offPeak->getMultiplier());
        }

        return [
            'baseCost' => $baseCost,
            'costUsd' => Decimals::roundHalfUp6($baseCost),
            'offPeakApplied' => $offPeakApplied,
        ];
    }

    private function piece(string $rate, int $tokens): string
    {
        Decimals::assertDecimal($rate);
        if ($tokens < 0) {
            throw new \DomainException('Количество токенов не может быть отрицательным.');
        }

        return Decimals::div(Decimals::mul((string) $tokens, $rate), self::TOKENS_PER_MILLION);
    }
}
