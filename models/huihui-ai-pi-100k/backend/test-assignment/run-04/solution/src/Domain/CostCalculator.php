<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

/**
 * Чистый расчёт стоимости одного события по строке цен и правилам тарификации:
 * схема тарификации, оф-пиковый множитель, единственное округление HALF_UP до 6 знаков.
 */
final class CostCalculator
{
    private const UNIT_DIVISOR = '1000000';

    /**
     * @param array{
     *   unit: string,
     *   input: ?string, output: ?string, cache_read: ?string, cache_write: ?string, any: ?string
     * } $price
     * @param array{start: string, end: string, multiplier: string}|null $offPeak
     */
    public function cost(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        array $price,
        ?array $offPeak,
        DateTimeImmutable $tsUtc,
    ): string {
        $base = $this->baseCost($tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite, $price);

        if ($offPeak !== null && TimeWindow::contains($tsUtc, $offPeak['start'], $offPeak['end'])) {
            $base = MoneyMath::mul($base, $offPeak['multiplier']);
        }

        return MoneyMath::roundHalfUp6($base);
    }

    /**
     * @param array{unit: string, input: ?string, output: ?string, cache_read: ?string, cache_write: ?string, any: ?string} $price
     */
    private function baseCost(int $in, int $out, int $cacheRead, int $cacheWrite, array $price): string
    {
        if ($price['unit'] === Unit::PER_1M_TOKENS) {
            return MoneyMath::add(
                MoneyMath::div(MoneyMath::mul((string) $in, (string) $price['input']), self::UNIT_DIVISOR),
                MoneyMath::add(
                    MoneyMath::div(MoneyMath::mul((string) $out, (string) $price['output']), self::UNIT_DIVISOR),
                    MoneyMath::add(
                        MoneyMath::div(MoneyMath::mul((string) $cacheRead, (string) $price['cache_read']), self::UNIT_DIVISOR),
                        MoneyMath::div(MoneyMath::mul((string) $cacheWrite, (string) $price['cache_write']), self::UNIT_DIVISOR),
                    ),
                ),
            );
        }

        if ($price['unit'] === Unit::PER_1M_TOKENS_UNIFIED) {
            $total = $in + $out + $cacheRead + $cacheWrite;

            return MoneyMath::div(MoneyMath::mul((string) $total, (string) $price['any']), self::UNIT_DIVISOR);
        }

        throw new \InvalidArgumentException(sprintf('Unknown pricing unit: %s', $price['unit']));
    }
}
