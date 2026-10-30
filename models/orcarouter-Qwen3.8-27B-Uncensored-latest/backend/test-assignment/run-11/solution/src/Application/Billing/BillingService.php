<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\EventCostCalculator;
use App\Domain\ImportDataException;
use App\Domain\Money;
use App\Domain\PriceRow;
use App\Domain\UsageEvent;
use App\Repository\DomainRepository;

/**
 * Тарификация: rate (расчёт ещё не рассчитанных) и reprice (ретро-коррекция).
 */
class BillingService
{
    public function __construct(
        private readonly DomainRepository $repo,
        private readonly EventCostCalculator $calculator,
    ) {}

    /**
     * Тарифицировать все pending/unrated события.
     *
     * @return array{rated: int, unrated: int, unrated_events: UsageEvent[]}
     */
    public function rate(): array
    {
        $candidates = $this->repo->findPendingOrUnrated();

        $rated = 0;
        $unrated = 0;
        $unratedEvents = [];

        foreach ($candidates as $event) {
            $provider = $this->repo->findProvider($event->getProviderCode());

            if ($provider === null) {
                $event->markUnrated();
                $unrated++;
                $unratedEvents[] = $event;
                continue;
            }

            $row = $this->repo->findRowEffectiveAt(
                $event->getProviderCode(),
                $event->getModelCode(),
                $event->getTs()
            );

            if ($row === null) {
                $event->markUnrated();
                $unrated++;
                $unratedEvents[] = $event;
                continue;
            }

            $cost = $this->calculator->finalCost(
                $row,
                $provider,
                $event->getTs(),
                $event->getTokenStats()
            );
            $event->applyRating($row, $cost);
            $rated++;
        }

        return [
            'rated' => $rated,
            'unrated' => $unrated,
            'unrated_events' => $unratedEvents,
        ];
    }

    /**
     * @param array<string, mixed> $correctionData
     *
     * @return array{reprice_events: int, delta: Money}
     */
    public function reprice(array $correctionData): array
    {
        $prices = $correctionData['prices'] ?? [];

        if (!is_array($prices)) {
            throw ImportDataException::new('"prices" must be an array');
        }

        $repriceEvents = 0;
        $delta = Money::zero();

        foreach ($prices as $raw) {
            if (!is_array($raw)) {
                continue;
            }

            $pc = $raw['provider'] ?? null;
            $mc = $raw['model'] ?? null;
            $validFromRaw = $raw['valid_from'] ?? null;
            $unit = $raw['unit'] ?? null;
            $currency = $raw['currency'] ?? null;
            $rates = $raw['rates'] ?? null;

            $this->validateCorrectionRow($pc, $mc, $validFromRaw, $unit, $currency, $rates);

            $provider = $this->repo->findProvider($pc);

            if ($provider === null) {
                throw ImportDataException::new("Provider '{$pc}' must exist");
            }

            $model = $this->repo->findModel($pc, $mc);

            if ($model === null) {
                throw ImportDataException::new("Model '{$pc}/{$mc}' must exist");
            }

            $validFrom = new \DateTimeImmutable(strval($validFromRaw));
            $ratesNormalized = $this->normalizeRates(is_array($rates) ? $rates : [], strval($unit));

            $newRow = new PriceRow(
                validFrom: $validFrom,
                currency: strval($currency),
                unit: strval($unit),
                rates: $ratesNormalized,
                model: $model,
            );

            $this->repo->upsertPriceRow($newRow);

            $effectiveRow = $this->repo->findPriceRow($pc, $mc, $validFrom);

            if ($effectiveRow === null) {
                continue;
            }

            foreach ($this->repo->findEventsByPriceRow($effectiveRow) as $event) {
                $oldCost = $event->getCost();

                if ($oldCost === null) {
                    continue;
                }

                $newCost = $this->calculator->finalCost(
                    $effectiveRow,
                    $provider,
                    $event->getTs(),
                    $event->getTokenStats()
                );
                $event->applyRating($effectiveRow, $newCost);
                $this->repo->saveUsageEvent($event);

                $repriceEvents++;
                $delta = $delta->add($newCost)->subtract($oldCost);
            }
        }

        return [
            'reprice_events' => $repriceEvents,
            'delta' => $delta,
        ];
    }

    private function validateCorrectionRow(
        mixed $pc,
        mixed $mc,
        mixed $validFromRaw,
        mixed $unit,
        mixed $currency,
        mixed $rates,
    ): void {
        if (!is_string($pc) || $pc === '') {
            throw ImportDataException::new('Correction needs a non-empty "provider"');
        }
        if (!is_string($mc) || $mc === '') {
            throw ImportDataException::new('Correction needs a non-empty "model"');
        }
        if (!is_string($validFromRaw) || $validFromRaw === '') {
            throw ImportDataException::new('Correction needs a valid "valid_from"');
        }
        if (!is_string($currency) || $currency === '') {
            throw ImportDataException::new('Correction needs a non-empty "currency"');
        }
        if (!is_string($unit) || $unit === '') {
            throw ImportDataException::new('Correction needs a non-empty "unit"');
        }
        if (!is_array($rates) || $rates === []) {
            throw ImportDataException::new('Correction needs non-empty "rates"');
        }
    }

    /**
     * @param array<string, mixed> $rates
     *
     * @return array<string, string>
     */
    private function normalizeRates(array $rates, string $unit): array
    {
        $required = match ($unit) {
            PriceRow::UNIT_PER_1M => ['input', 'output', 'cache_read', 'cache_write'],
            PriceRow::UNIT_PER_1M_UNIFIED => ['any'],
            default => throw new ImportDataException("Unknown unit '{$unit}'"),
        };

        $result = [];
        foreach ($required as $key) {
            $value = $rates[$key] ?? null;

            if ($value === null) {
                throw ImportDataException::new("Missing rate '{$key}'");
            }
            if (!is_string($value) || !preg_match('/^\d+(\.\d+)?$/', $value)) {
                throw ImportDataException::new("Rate '{$key}' must be a non-negative decimal string");
            }
            $result[$key] = $value;
        }

        return $result;
    }
}
