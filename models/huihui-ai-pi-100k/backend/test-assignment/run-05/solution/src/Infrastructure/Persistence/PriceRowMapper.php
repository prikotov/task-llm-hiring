<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Billing\PriceRowSpec;
use App\Infrastructure\Persistence\Entity\PriceRow;

/**
 * Перевод сущности цены в предметную модель домена и обратно.
 */
final class PriceRowMapper
{
    public static function toSpec(PriceRow $row): PriceRowSpec
    {
        return match ($row->unit) {
            PriceRowSpec::UNIT_PER_TOKEN => PriceRowSpec::perToken(
                (string) ($row->rateInput ?? '0'),
                (string) ($row->rateOutput ?? '0'),
                (string) ($row->rateCacheRead ?? '0'),
                (string) ($row->rateCacheWrite ?? '0'),
            ),
            PriceRowSpec::UNIT_UNIFIED => PriceRowSpec::unified((string) ($row->rateAny ?? '0')),
            default => throw new \DomainException(sprintf('Неизвестная схема тарификации: "%s"', $row->unit)),
        };
    }

    public static function apply(PriceRow $row, PriceRowSpec $spec, string $currency): void
    {
        $row->currency = $currency;
        $row->unit = $spec->unit;

        if ($spec->isPerToken()) {
            $row->rateInput = $spec->rate('input');
            $row->rateOutput = $spec->rate('output');
            $row->rateCacheRead = $spec->rate('cache_read');
            $row->rateCacheWrite = $spec->rate('cache_write');
            $row->rateAny = null;
        } else {
            $row->rateInput = null;
            $row->rateOutput = null;
            $row->rateCacheRead = null;
            $row->rateCacheWrite = null;
            $row->rateAny = $spec->rate('any');
        }
    }
}
