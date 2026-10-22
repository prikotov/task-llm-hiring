<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\PriceRow;
use App\Domain\Model\Provider;
use App\Domain\Model\UsageEvent;
use App\Domain\ValueObject\Money;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Класс тарификации одного события (п. 1–5 требований).
 */
final class Rater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceBook $priceBook,
    ) {
    }

    /**
     * Возвращает стоимость либо null (unrated). Стоимость уже округлёна до 6
     * знаков по HALF_UP.
     */
    public function price(UsageEvent $event): ?string
    {
        $priceRow = $this->priceBook->findPrice($event->providerCode.'/'.$event->modelCode, $event->eventTs);
        if (null === $priceRow) {
            return null;
        }

        $base = $this->baseCost($priceRow, $event);

        $offPeak = $this->offPeak($event);
        if (null !== $offPeak) {
            $base = Money::multiply($base, $offPeak);
        }

        return Money::round($base);
    }

    private function baseCost(PriceRow $priceRow, UsageEvent $event): string
    {
        if (PriceRow::UNIT_UNIFIED === $priceRow->unit) {
            $total = $event->getAnyTokens();
            if (0 === $total) {
                return '0';
            }

            return Money::cost($total, $priceRow->getAnyRate());
        }

        // per_1m_tokens
        $types = [
            'input' => $event->tokensInput,
            'output' => $event->tokensOutput,
            'cache_read' => $event->tokensCacheRead,
            'cache_write' => $event->tokensCacheWrite,
        ];

        $cost = '0';
        foreach ($types as $kind => $tokens) {
            if (0 === $tokens || !$priceRow->hasRate($kind)) {
                continue;
            }
            $cost = Money::add($cost, Money::cost($tokens, $priceRow->getRate($kind)));
        }

        return $cost;
    }

    private function offPeak(UsageEvent $event): ?string
    {
        /** @var Provider|null $provider */
        $provider = $this->priceBook->provider($event->providerCode);
        if (null === $provider || null === $provider->windowStart || null === $provider->windowEnd) {
            return null;
        }

        $hour = (int) $event->eventTs->format('G');
        $minute = (int) $event->eventTs->format('i');
        if (!$provider->isOffPeak($hour, $minute)) {
            return null;
        }

        return $provider->offPeakMultiplier;
    }
}
