<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Entity\PriceRow;
use App\Entity\UsageEvent;

/**
 * Расчёт стоимости одного события по строке цен и оф-пику провайдера.
 *
 * Все операции — десятичная арифметика bcmath без промежуточных округлений;
 * единственный round-оператор на выходе (HALF_UP до 6 знаков).
 */
final class RateCalculator
{
    /**
     * Итоговая стоимость события (уже с оф-пиком и итоговым округлением).
     */
    public function calculate(UsageEvent $event, PriceRow $row, OffPeakWindow|null $offPeak): Money
    {
        $unit = $row->getUnit();
        $cost = '0';

        if (Unit::PER_1M_TOKENS === $unit) {
            $perKind = [
                Unit::KIND_INPUT => $event->getTokensInput(),
                Unit::KIND_OUTPUT => $event->getTokensOutput(),
                Unit::KIND_CACHE_READ => $event->getTokensCacheRead(),
                Unit::KIND_CACHE_WRITE => $event->getTokensCacheWrite(),
            ];

            foreach ($perKind as $kind => $tokens) {
                if (0 === $tokens) {
                    continue;
                }

                $rate = $row->rateFor($kind);

                if (null === $rate) {
                    continue;
                }

                // tokens * rate / 1_000_000
                $cost = DecimalMath::add($cost, DecimalMath::div(DecimalMath::mul((string) $tokens, $rate), DecimalMath::TOKEN_DIVISOR));
            }
        } elseif (Unit::PER_1M_TOKENS_UNIFIED === $unit) {
            $total = $event->getTokensInput()
                + $event->getTokensOutput()
                + $event->getTokensCacheRead()
                + $event->getTokensCacheWrite();

            if (0 !== $total) {
                $rate = $row->rateFor(Unit::KIND_ANY);

                if (null !== $rate) {
                    $cost = DecimalMath::div(DecimalMath::mul((string) $total, $rate), DecimalMath::TOKEN_DIVISOR);
                }
            }
        } else {
            throw new \DomainException(sprintf('Неизвестная схема тарификации: "%s"', $unit));
        }

        if (null !== $offPeak && $offPeak->covers($event->getTs())) {
            $cost = DecimalMath::mul($cost, $offPeak->multiplier);
        }

        return Money::of($cost)->roundTo6();
    }
}
