<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Entity\Provider;
use App\Entity\Price;
use App\Entity\UsageEvent;

/**
 * Тарификация события по справочнику цен с учётом оф-пиковой скидки провайдера.
 *
 * Стоимость вычисляется точно (без промежуточных округлений):
 * Σ tokens × rate / 1 000 000, затем (если время суток ts в окне провайдера)
 * × multiplier_офпик. Округление ровно одно — HALF_UP до 6 знаков при
 * оформлении итога события (см. Decimal::roundHalfUp6()).
 */
final class EventPricingService
{
    public const DIVISOR = 1_000_000;

    /**
     * Возвращает null, если на момент события у модели не было строки цены.
     *
     * @return array{cost: Decimal, price: Price}|null
     */
    public function rateEvent(UsageEvent $event): ?array
    {
        $provider = $event->getProvider();
        $model = $event->getModel();
        $ts = $event->getTs();

        // Выбор цены: наибольшее valid_from ≤ ts (`valid_from` включительно).
        $best = null;
        foreach ($model->getPrices() as $price) {
            if ($price->getValidFrom() > $ts) {
                continue;
            }
            if ($best === null || $price->getValidFrom() > $best->getValidFrom()) {
                $best = $price;
            }
        }
        if ($best === null) {
            return null;
        }

        $priceRow = ['unit' => $best->getUnit(), 'rates' => $best->getRates()];
        $cost = $this->computeCost($event, $priceRow, $provider);

        return ['cost' => $cost, 'price' => $best];
    }

    /**
     * @param array{unit: string, rates: array<string, string>} $priceRow
     */
    private function computeCost(
        UsageEvent $event,
        array $priceRow,
        Provider $provider,
    ): Decimal {
        $unit = $priceRow['unit'];
        $rates = $priceRow['rates'];

        if ($unit === Provider::UNIT_PER_TOKEN) {
            $cost = Decimal::fromInt(0);
            $cost = $cost
                ->add(Decimal::fromInt($event->getTokensInput())->multiply(Decimal::fromString($rates['input'] ?? '0')))
                ->add(Decimal::fromInt($event->getTokensOutput())->multiply(Decimal::fromString($rates['output'] ?? '0')))
                ->add(Decimal::fromInt($event->getTokensCacheRead())->multiply(Decimal::fromString($rates['cache_read'] ?? '0')))
                ->add(Decimal::fromInt($event->getTokensCacheWrite())->multiply(Decimal::fromString($rates['cache_write'] ?? '0')));
        } elseif ($unit === Provider::UNIT_UNIFIED) {
            $total = $event->getTokensInput()
                + $event->getTokensOutput()
                + $event->getTokensCacheRead()
                + $event->getTokensCacheWrite();
            $cost = Decimal::fromInt($total)->multiply(Decimal::fromString($rates['any'] ?? '0'));
        } else {
            throw new \DomainException(sprintf('Неизвестная схема тарификации: "%s".', $unit));
        }

        // Деление на 1 000 000 — точно (знаменатель = 2^6 · 5^6, делится без остатка).
        $parts = $cost->parts();
        $p = ltrim($parts['p'], '0');
        if ($p === '') {
            $p = '0';
        }
        $divided = Decimal::exactDivide($p, $parts['k'], self::DIVISOR);
        if ($parts['negative']) {
            $divided = $divided->negate();
        }

        // Оф-пик: время суток ts в окне [start, end), окно может переходить полночь.
        if ($provider->hasOffPeak()) {
            $g = $event->getTs()->format('Gi');
            $minuteOfDay = (int) substr($g, 0, 2) * 60 + (int) substr($g, 2, 2);
            if ($provider->isOffPeak($minuteOfDay)) {
                $divided = $divided->multiply(Decimal::fromString($provider->getOffPeakMultiplier() ?? '1'));
            }
        }

        return $divided;
    }
}
