<?php

declare(strict_types=1);

namespace App\Domain\Pricing\Service;

use App\Application\Money\Money;
use App\Domain\Pricing\Entity\Price;
use App\Domain\Pricing\ValueObject\PricingUnit;
use App\Domain\Usage\Entity\UsageEvent;
use InvalidArgumentException;

/**
 * Считает стоимость одного события по выбранной строке цен и оф-пиковому
 * окну провайдера. Возвращает уже округлённую до 6 знаков стоимость.
 */
final class PricingCalculator
{
    private const TOKEN_KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    public function calculate(UsageEvent $event, Price $price): string
    {
        $rates = $price->rates();
        $numerator = '0';

        if ($price->unit() === PricingUnit::Per1mTokens) {
            foreach (self::TOKEN_KINDS as $kind) {
                if (!array_key_exists($kind, $rates)) {
                    throw new InvalidArgumentException(sprintf('Missing "%s" rate.', $kind));
                }

                $tokens = $event->{'tokens'.str_replace('_', '', ucwords($kind, '_'))}();
                $numerator = Money::add(
                    $numerator,
                    Money::mul((string) $tokens, $rates[$kind]),
                );
            }
        } else {
            if (!array_key_exists('any', $rates)) {
                throw new InvalidArgumentException('Missing "any" rate.');
            }

            $tokens = $event->tokensInput()
                + $event->tokensOutput()
                + $event->tokensCacheRead()
                + $event->tokensCacheWrite();

            $numerator = Money::mul((string) $tokens, $rates['any']);
        }

        $cost = Money::div($numerator, '1000000');

        $offPeak = $price->provider()->offPeakWindow();
        if ($offPeak !== null && $offPeak->contains($event->occurredAt())) {
            $cost = Money::mul($cost, $offPeak->multiplier());
        }

        return Money::roundHalfUp($cost);
    }
}
