<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\PriceLine;
use App\Domain\UsageEvent;
use App\Domain\Provider;
use App\Domain\ValueObject\Money;
use App\Repository\PriceLineRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;

/**
 * Pure rating domain service. Deterministic given (event, price line, off-peak).
 *
 * All amounts use exact decimal arithmetic (bcmath scale 20); rounding is a
 * single HALF_UP to 6 places at the very end of the event's cost.
 */
final class RatingService
{
    private const INV_MILLION = '0000001'; // bcmath scale 20; 1/10^6

    public function __construct(
        private readonly PriceLineRepository $priceLines,
        private readonly ProviderRepository $providers,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @return iterable<array{event:UsageEvent, line:PriceLine, cost:Money, provider:Provider|null}>
     */
    public function rateEvent(UsageEvent $event): ?array
    {
        $line = $this->priceLines->pickForInstant($event->providerCode(), $event->modelCode(), $event->tsUtc());
        if ($line === null) {
            return null;
        }
        $provider = $this->providers->findByCode($event->providerCode());
        $cost = $line->price()->costFor(
            $event->tokensInput(),
            $event->tokensOutput(),
            $event->tokensCacheRead(),
            $event->tokensCacheWrite(),
        );
        if ($provider !== null) {
            $off = $provider->offPeakFromStorage();
            if ($off !== null && $off->containsUtc($event->tsUtc())) {
                $cost = $cost->multiplyBy($off->multiplier());
            }
        }
        return [
            'event' => $event,
            'line' => $line,
            'cost' => $cost,
            'provider' => $provider,
        ];
    }

    /**
     * Round a Money to 6 places and format (always 6 decimals).
     */
    public function eventCostString(Money $cost): string
    {
        return $cost->roundTo(6)->format(6);
    }

    /**
     * Sum of Money events, format to 6 decimals (sum is already exact, no
     * re-round of addends is required by the spec).
     */
    public function formatSum(Money $sum): string
    {
        return $sum->format(6);
    }
}
