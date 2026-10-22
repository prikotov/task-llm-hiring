<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Event;
use App\Domain\Entity\ModelPrice;
use App\Domain\Entity\Provider;
use App\ValueObject\Pricing;

/**
 * In-memory index of price lines keyed by provider+model, plus a map of events
 * for scoring and reporting. Rebuilt each run from imported data. Not
 * persisted.
 */
final class PriceIndex
{
    /** @var array<string, array<string, list<ModelPrice>>> [provider => model => lines sorted by valid_from desc] */
    private array $byModel = [];

    /** @var array<string, Provider> */
    private array $providers = [];

    /** @var array<string, Event> keyed by provider|provider_request_id */
    private array $events = [];

    public function setProviders(array $providers): void
    {
        $this->providers = [];
        foreach ($providers as $p) {
            $this->providers[$p->providerCode] = $p;
        }
    }

    public function getProvider(string $code): ?Provider
    {
        return $this->providers[$code] ?? null;
    }

    /**
     * @param list<ModelPrice> $prices
     */
    public function setPrices(array $prices): void
    {
        $this->byModel = [];
        foreach ($prices as $pl) {
            $this->byModel[$pl->provider][$pl->model][] = $pl;
        }
        // Keep each model's lines sorted by descending valid_from.
        foreach ($this->byModel as $provider => $models) {
            foreach ($models as $model => $lines) {
                usort($lines, static fn (ModelPrice $a, ModelPrice $b): int => $b->validFrom <=> $a->validFrom);
                $this->byModel[$provider][$model] = $lines;
            }
        }
    }

    /**
     * Register events so the index can score/report them.
     *
     * @param list<Event> $events
     */
    public function setEvents(array $events): void
    {
        $this->events = [];
        foreach ($events as $event) {
            $this->events[$event->provider.'|'.$event->providerRequestId] = $event;
        }
    }

    /**
     * Find the applicable price line for a given model and event timestamp:
     * the line with the greatest valid_from that is <= $ts.
     */
    public function findPrice(string $provider, string $model, \DateTimeImmutable $ts): ?ModelPrice
    {
        $lines = $this->byModel[$provider][$model] ?? [];
        foreach ($lines as $line) {
            if ($line->validFrom <= $ts) {
                return $line;
            }
        }

        return null;
    }

    /**
     * The applicable pricing (price line + provider) for one event.
     */
    public function findPricing(Event $event): ?Pricing
    {
        $line = $this->findPrice($event->provider, $event->model, $event->timestamp);
        if ($line === null) {
            return null;
        }
        $provider = $this->getProvider($line->provider);
        if (null === $provider) {
            return null;
        }

        return new Pricing($line->copy(), $provider->copy());
    }

    /**
     * @return list<Event>
     */
    public function listEvents(): array
    {
        return array_values($this->events);
    }

    public function getEvent(string $provider, string $id): ?Event
    {
        return $this->events[$provider.'|'.$id] ?? null;
    }

    public function countEvents(): int
    {
        return count($this->events);
    }

    public function resetEvents(): void
    {
        $this->events = [];
    }
}
