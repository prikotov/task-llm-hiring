<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\Exception\BillingException;

/**
 * Расчёт стоимости одного события: схема тарификации, цены,
 * опциональная оф-пиковая скидка.
 *
 * Правила (TASK.md п. 2, 3, 4):
 *  - per_1m_tokens:        cost = Σ tokens_вид × rate_вид / 1_000_000;
 *  - per_1m_tokens_unified: cost = (Σ tokens_вид) × rate_any / 1_000_000;
 *  - при попадании ts в оф-пиковое окно провайдера итог умножается на
 *    multiplier окна;
 *  - единственное округление — итог события HALF_UP до 6 знаков;
 *  - float не используется (bcmath).
 */
final class RateCalculator
{
    private const DIVISOR = '1000000';

    public function calculate(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        PriceSheet $sheet,
        ?OffPeakWindow $offPeak,
        \DateTimeImmutable $ts,
    ): Money {
        foreach ([$tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite] as $t) {
            if ($t < 0) {
                throw BillingException::invalidValue('количество токенов не может быть отрицательным');
            }
        }
        $sum = Money::zero();

        switch ($sheet->unit) {
            case 'per_1m_tokens':
                $sum = $sum
                    ->add(self::tokensTimesRate($tokensInput, $sheet->rate('input')))
                    ->add(self::tokensTimesRate($tokensOutput, $sheet->rate('output')))
                    ->add(self::tokensTimesRate($tokensCacheRead, $sheet->rate('cache_read')))
                    ->add(self::tokensTimesRate($tokensCacheWrite, $sheet->rate('cache_write')));
                break;

            case 'per_1m_tokens_unified':
                $total = $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite;
                $sum = self::tokensTimesRate($total, $sheet->rate('any'));
                break;

            default:
                throw BillingException::invalidValue(
                    sprintf('Неизвестная схема тарификации: %s', $sheet->unit)
                );
        }

        if ($offPeak !== null && $offPeak->contains($ts)) {
            $sum = $sum->multiply($offPeak->multiplier());
        }

        // единственное округление: итог события — HALF_UP 6 знаков
        return $sum->round(6);
    }

    /** tokens × rate / 1_000_000 (без округления). */
    private static function tokensTimesRate(int $tokens, Money $rate): Money
    {
        return Money::of($tokens)
            ->multiply($rate)
            ->divide(Money::of(self::DIVISOR));
    }
}
