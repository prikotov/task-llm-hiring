<?php

declare(strict_types=1);

namespace App\Billing\Math;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;

/**
 * Вычисление стоимости одного события по текущим справочным данным
 * (выбранный price + off-peak провайдера).
 */
final class PricingEngine
{
    /**
     * Выбрать стоимость события:
     *   1) найти max(valid_from <= ts),
     *   2) применить схему unit,
     *   3) применить off-peak провайдера,
     *   4) округлить HALF_UP до 6 знаков.
     *
     * @param \App\Entity\Provider[] $providersByCode
     * @param \App\Entity\Model[] $modelsByProviderCodeModelCode  ключ "provider|model"
     * @param \App\Entity\Price[] $pricesByModelCode  ключ "provider|model" -> список Price
     * @return array{cost: string|null, price: \App\Entity\Price|null, model: \App\Entity\Model|null}
     */
    public function priceEvent(
        UsageEvent $event,
        array $pricesByProviderCode,
        array $modelsByProviderCodeModelCode,
        array $providersByCode
    ): array {
        $provider = $providersByCode[$event->getProviderCode()] ?? null;
        if ($provider === null) {
            return ['cost' => null, 'price' => null, 'model' => null];
        }

        $modelKey = $event->getProviderCode() . '|' . $event->getModelCode();
        $model = $modelsByProviderCodeModelCode[$modelKey] ?? null;
        if ($model === null) {
            return ['cost' => null, 'price' => null, 'model' => null];
        }

        $prices = $pricesByProviderCode[$event->getProviderCode()] ?? [];
        $ts = $event->getTs();

        // Найти max(valid_from <= ts)
        $chosenPrice = null;
        $bestFrom = null;
        foreach ($prices as $price) {
            $vf = $price->getValidFrom();
            if ($vf <= $ts) {
                if ($bestFrom === null || $vf > $bestFrom) {
                    $chosenPrice = $price;
                    $bestFrom = $vf;
                }
            }
        }

        if ($chosenPrice === null) {
            return ['cost' => null, 'price' => null, 'model' => $model];
        }

        $cost = $this->computeCost($event, $chosenPrice);

        if ($provider->hasOffPeak()) {
            $cost = $this->applyOffPeak($provider, $ts, $cost);
        }

        $cost = MoneyMath::roundHalfUp6($cost);

        return ['cost' => $cost, 'price' => $chosenPrice, 'model' => $model];
    }

    private function computeCost(UsageEvent $event, Price $price): string
    {
        if ($price->getUnit() === 'per_1m_tokens_unified') {
            $anyRate = $price->getRateAny();
            $tokensTotal = (string) ($event->getTokensInput() + $event->getTokensOutput()
                + $event->getTokensCacheRead() + $event->getTokensCacheWrite());
            return MoneyMath::div(MoneyMath::mul($tokensTotal, $anyRate ?? '0'), '1000000');
        }

        $ri = $price->getRateInput() ?? '0';
        $ro = $price->getRateOutput() ?? '0';
        $rcr = $price->getRateCacheRead() ?? '0';
        $rcw = $price->getRateCacheWrite() ?? '0';

        $sum = '0';
        $sum = bcadd($sum, MoneyMath::div(MoneyMath::mul((string) $event->getTokensInput(), $ri), '1000000'), MoneyMath::SCALE);
        $sum = bcadd($sum, MoneyMath::div(MoneyMath::mul((string) $event->getTokensOutput(), $ro), '1000000'), MoneyMath::SCALE);
        $sum = bcadd($sum, MoneyMath::div(MoneyMath::mul((string) $event->getTokensCacheRead(), $rcr), '1000000'), MoneyMath::SCALE);
        $sum = bcadd($sum, MoneyMath::div(MoneyMath::mul((string) $event->getTokensCacheWrite(), $rcw), '1000000'), MoneyMath::SCALE);

        return $sum;
    }

    private function applyOffPeak(Provider $provider, string $ts, string $cost): string
    {
        $timePart = substr($ts, 11, 5);
        if (!$provider->isOffPeak($timePart)) {
            return $cost;
        }
        return MoneyMath::mul($cost, $provider->getOffPeakMultiplier());
    }
}
