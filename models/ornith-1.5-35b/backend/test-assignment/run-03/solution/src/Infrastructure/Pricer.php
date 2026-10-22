<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Decimal;
use App\Domain\Entity\OffPeakWindow;
use App\Domain\Entity\PriceRow;

/**
 * Класс тарификации одного события.
 *
 * Возвращает итоговую стоимость события с округлением до 6 знаков HALF_UP,
 * либо null, если цена не найдена.
 */
final class Pricer
{
    /** коэффициент перевода «за миллион токенов» в «за один токен». */
    public const PER_MILLION = '1000000';

    public function price(
        PriceRow $priceRow,
        OffPeakWindow $offPeak,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): ?string {
        $base = match ($priceRow->unit) {
            'per_1m_tokens' => $priceRow->costPer1m(
                $tokensInput,
                $tokensOutput,
                $tokensCacheRead,
                $tokensCacheWrite,
            ),
            'per_1m_tokens_unified' => $priceRow->costUnified(
                $tokensInput,
                $tokensOutput,
                $tokensCacheRead,
                $tokensCacheWrite,
            ),
            default => throw new \InvalidArgumentException(
                'Неизвестная схема тарификации: ' . $priceRow->unit,
            ),
        };

        // ставки заданы за 1 000 000 токенов → делим на миллион.
        $perToken = $base->divide(self::PER_MILLION);

        if ($offPeak->isActive() && $offPeak->contains($this->secondsFromMidnight($ts))) {
            $perToken = $perToken->multiply($offPeak->multiplier());
        }

        return Decimal::halfUp($perToken->toString());
    }

    /**
     * Секунды от полуночи UTC для заданного момента (0..86399).
     */
    private function secondsFromMidnight(\DateTimeImmutable $ts): int
    {
        $utc = $ts->setTimezone(new \DateTimeZone('UTC'));

        return (int) $utc->format('H') * 3600 + (int) $utc->format('i') * 60
            + (int) $utc->format('s');
    }
}
