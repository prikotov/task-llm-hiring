<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Money\MoneyMath;

/**
 * Расчёт стоимости события (TASK.md, правила 1–4).
 *
 * cost = Σ tokens_вид × rate_вид / 1 000 000          (per_1m_tokens)
 * cost = (Σ tokens) × rate_any / 1 000 000            (per_1m_tokens_unified)
 *
 * Затем (если время события попадает в оф-пик провайдера) —
 * cost × multiplier. Единственное округление — HALF_UP до 6 знаков.
 */
final class EventPricer
{
    public function price(
        PriceRowSpec $row,
        TokenUsage $usage,
        ?OffPeak $offPeak,
        \DateTimeImmutable $eventTimeUtc,
    ): string {
        $base = $row->isPerToken()
            ? MoneyMath::sum([
                MoneyMath::mul((string) $usage->input, $row->rate('input')),
                MoneyMath::mul((string) $usage->output, $row->rate('output')),
                MoneyMath::mul((string) $usage->cacheRead, $row->rate('cache_read')),
                MoneyMath::mul((string) $usage->cacheWrite, $row->rate('cache_write')),
            ])
            : ($row->isUnified()
                ? MoneyMath::mul((string) $usage->total(), $row->rate('any'))
                : throw new \DomainException(sprintf('Неизвестная схема тарификации: "%s"', $row->unit)));

        $cost = MoneyMath::div($base, MoneyMath::TOKENS_PER_UNIT);

        if ($offPeak !== null && $offPeak->isActiveAt($eventTimeUtc)) {
            $cost = MoneyMath::mul($cost, $offPeak->multiplier);
        }

        return MoneyMath::round($cost);
    }
}
