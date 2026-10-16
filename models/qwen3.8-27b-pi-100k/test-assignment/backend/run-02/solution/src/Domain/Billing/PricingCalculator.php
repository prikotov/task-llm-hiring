<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Exception\BillingException;
use App\Entity\Price;
use DateTimeImmutable;

/**
 * Расчёт точной стоимости события по правилам тарификации:
 *
 * - per_1m_tokens:        cost = SUM(tokens_вид * rate_вид) / 1 000 000;
 * - per_1m_tokens_unified: cost = (сумма всех токенов) * rate_any / 1 000 000;
 * - оф-пик: если время суток (UTC) в окне провайдера, стоимость всего
 *   события умножается на множитель;
 * - всё считается без округления (bcmath), единственное округление —
 *   итоговая стоимость события до 6 знаков HALF_UP.
 */
final class PricingCalculator
{
    private const PER_MILLION = '1000000';

    public function price(
        Price $row,
        TokenUsage $tokens,
        ?OffPeakPolicy $offPeak = null,
        ?DateTimeImmutable $eventTime = null,
    ): string {
        $cost = match ($row->unit()) {
            Price::UNIT_PER_TOKEN => $this->pricePerToken($row->rates(), $tokens),
            Price::UNIT_UNIFIED => Money::div(
                Money::mul((string) $tokens->total(), $this->rate($row->rates(), 'any')),
                self::PER_MILLION,
            ),
            default => throw new BillingException(sprintf('Неизвестная схема тарификации: %s', $row->unit())),
        };

        if ($offPeak !== null && $eventTime !== null && $offPeak->applies($eventTime)) {
            $cost = Money::mul($cost, $offPeak->multiplier());
        }

        return Money::roundHalfUp($cost);
    }

    private function pricePerToken(array $rates, TokenUsage $tokens): string
    {
        $sum = '0';

        foreach ([
            'input' => $tokens->input(),
            'output' => $tokens->output(),
            'cache_read' => $tokens->cacheRead(),
            'cache_write' => $tokens->cacheWrite(),
        ] as $kind => $count) {
            $sum = Money::add(
                $sum,
                Money::div(Money::mul((string) $count, $this->rate($rates, $kind)), self::PER_MILLION),
            );
        }

        return $sum;
    }

    /**
     * @param array<string, string> $rates
     */
    private function rate(array $rates, string $kind): string
    {
        if (!isset($rates[$kind]) || !\is_scalar($rates[$kind])) {
            throw new BillingException(sprintf("В строке цен нет ставки вида '%s'", $kind));
        }

        return (string) $rates[$kind];
    }
}
