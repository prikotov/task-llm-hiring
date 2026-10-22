<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use InvalidArgumentException;

/**
 * Модель провайдера. Код уникален в рамках провайдера.
 */
final class Model
{
    public function __construct(
        public readonly string $code,
        public ?int $id = null,
    ) {
        if ('' === $code) {
            throw new InvalidArgumentException('Код модели не может быть пустым.');
        }
    }

    /**
     * Цена, действующая в момент $moment: строка с наибольшим validFrom <= $moment.
     *
     * @param iterable<PriceRow> $rows строки цен модели (порядок не важен)
     */
    public function priceAt(iterable $rows, \DateTimeImmutable $moment): ?PriceRow
    {
        $best = null;
        foreach ($rows as $row) {
            if ($row->validFrom > $moment) {
                continue;
            }
            if (null === $best || $row->validFrom > $best->validFrom) {
                $best = $row;
            }
        }

        return $best;
    }

    public function costOf(PriceRow $row, \App\Domain\Usage\TokenUsage $tokens): Decimal
    {
        return match ($row->unit) {
            PricingUnit::Per1MTokens => Decimal::fromInt($tokens->input)->mul($row->rate(TokenKind::Input->value))
                ->add(Decimal::fromInt($tokens->output)->mul($row->rate(TokenKind::Output->value)))
                ->add(Decimal::fromInt($tokens->cacheRead)->mul($row->rate(TokenKind::CacheRead->value)))
                ->add(Decimal::fromInt($tokens->cacheWrite)->mul($row->rate(TokenKind::CacheWrite->value)))
                ->div(Decimal::fromInt(1_000_000)),
            PricingUnit::Per1MTokensUnified => Decimal::fromInt($tokens->total())
                ->mul($row->rate('any'))
                ->div(Decimal::fromInt(1_000_000)),
        };
    }
}
