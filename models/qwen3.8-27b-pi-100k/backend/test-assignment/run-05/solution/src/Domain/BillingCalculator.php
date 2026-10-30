<?php

declare(strict_types=1);

namespace App\Domain;

use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\PriceUnit;

final class BillingCalculator
{
    private const TOKENS_DENOMINATOR = '1000000';

    /**
     * Точная стоимость события до финального округления (масштаб Money::SCALE).
     *
     * @return string|null null, если по строке цен событие рассчитать нельзя
     */
    public static function computeExact(
        PriceRow $row,
        ?Provider $provider,
        \DateTimeImmutable $tsUtc,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): ?string {
        if ($row->getUnit() === PriceUnit::PER_1M_TOKENS) {
            $total = Money::add(Money::mul((string) $tokensInput, (string) ($row->getRateInput() ?? '0')),
                Money::mul((string) $tokensOutput, (string) ($row->getRateOutput() ?? '0')));
            $total = Money::add($total, Money::mul((string) $tokensCacheRead, (string) ($row->getRateCacheRead() ?? '0')));
            $total = Money::add($total, Money::mul((string) $tokensCacheWrite, (string) ($row->getRateCacheWrite() ?? '0')));
        } elseif ($row->getUnit() === PriceUnit::PER_1M_TOKENS_UNIFIED) {
            if ($row->getRateAny() === null) {
                return null;
            }
            $allTokens = $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite;
            $total = Money::mul((string) $allTokens, $row->getRateAny());
        } else {
            return null;
        }

        $total = bcdiv($total, self::TOKENS_DENOMINATOR, Money::SCALE);

        if ($provider !== null && OffPeakWindow::contains($provider, $tsUtc)) {
            $total = Money::mul($total, (string) $provider->getOffPeakMultiplier());
        }

        return $total;
    }
}
