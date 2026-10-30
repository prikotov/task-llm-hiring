<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Money\DecimalMoney;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceRow;

enum RatingStatus: string
{
    case RATED = 'rated';
    case UNRATED = 'unrated';
}

final class RatingResult
{
    private function __construct(
        public readonly RatingStatus $rating,
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly ?PriceRow $priceRow,
        public readonly ?OffPeakWindow $offPeak,
        public readonly bool $offPeakApplied,
        public readonly DecimalMoney $exactCost,
        public readonly DecimalMoney $cost,
    ) {
    }

    public static function rated(
        string $providerCode,
        string $modelCode,
        PriceRow $priceRow,
        ?OffPeakWindow $offPeak,
        bool $offPeakApplied,
        DecimalMoney $exactCost,
        DecimalMoney $cost,
    ): self {
        return new self(
            rating: RatingStatus::RATED,
            providerCode: $providerCode,
            modelCode: $modelCode,
            priceRow: $priceRow,
            offPeak: $offPeak,
            offPeakApplied: $offPeakApplied,
            exactCost: $exactCost,
            cost: $cost,
        );
    }

    public static function unrated(string $providerCode, string $modelCode): self
    {
        $zero = DecimalMoney::zero();

        return new self(
            rating: RatingStatus::UNRATED,
            providerCode: $providerCode,
            modelCode: $modelCode,
            priceRow: null,
            offPeak: null,
            offPeakApplied: false,
            exactCost: $zero,
            cost: $zero,
        );
    }
}
