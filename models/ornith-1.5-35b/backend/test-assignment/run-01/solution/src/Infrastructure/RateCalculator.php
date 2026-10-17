<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Decimal;
use App\Entity\Price;
use App\Entity\UsageEvent;

/**
 * Расчёт стоимости одного события использования.
 *
 * Правила (закрытие в Rate):
 *   - выбор строки цены: max(valid_from) <= ts;
 *   - схема per_1m_tokens / per_1m_tokens_unified;
 *   - оф-пиковая скидка в окне UTC провайдера, если ts в нём —
 *     стоимость умножается на multiplier;
 *   - rounding события: один раз, до 6 знаков, HALF_UP;
 *   - деньги — только bcmath через Decimal, float запрещён.
 *
 * Для unrated-события (без строки цены) — исключение.
 */
final class RateCalculator
{
    /** @var callable(null|Price): Price */
    private $priceBy;

    public function __construct(callable $priceBy)
    {
        $this->priceBy = $priceBy;
    }

    public function rate(UsageEvent $event): Decimal
    {
        $price = ($this->priceBy)($event);
        if ($price === null) {
            throw new \RuntimeException(
                'unrated event ' . $event->getProvider()->getCode() . '/' . $event->getRequestId()
            );
        }

        return $this->rateFor($price, $event);
    }

    /**
     * Расчёт стоимости для уже выбранной строки цены (без null-проверки).
     */
    public function rateFor(Price $price, UsageEvent $event): Decimal
    {
        $tokens = $this->tokens($event);
        $multiplier = $this->offPeakMultiplier($event, $price);

        $result = (new Rate($price))
            ->calculateWithDiscount($tokens->toArray(), $multiplier);

        return $result->round(Decimal::ROUND_SCALE);
    }

    private function offPeakMultiplier(UsageEvent $event, Price $price): ?string
    {
        $period = $price->getProvider()->getOffPeakPeriod();
        if ($period === null) {
            return null;
        }

        return $period->isAt($event->getInstant())
            ? $period->getDiscountMultiplier()->toString()
            : null;
    }

    private function tokens(UsageEvent $event): Tokens
    {
        $t = $event->getTokens();

        return Tokens::of(
            (int) $t[0],
            (int) $t[1],
            (int) $t[2],
            (int) $t[3]
        );
    }}
