<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Billing\Cost;
use App\Domain\Billing\PriceCatalog;
use App\Domain\Billing\ProviderInfo;
use App\Domain\Billing\RatedEventStore;
use App\Domain\Pricing\StoredPriceRow;
use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageEventRepository;

/**
 * In-memory реализация PriceCatalog для юнит-тестов.
 *
 * @internal
 */
final class InMemoryCatalog implements PriceCatalog
{
    /**
     * @param array<string, StoredPriceRow> $rows
     */
    public function __construct(
        private array $rows,
        private ?ProviderInfo $prismInfo,
    ) {
    }

    public function findProvider(string $code): ?ProviderInfo
    {
        return $this->prismInfo;
    }

    public function findPriceRowFor(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $ts,
    ): ?StoredPriceRow {
        $best = null;
        foreach ($this->rows as $r) {
            if ($r->provider !== $providerCode || $r->model !== $modelCode) {
                continue;
            }
            $vf = new \DateTimeImmutable($r->validFrom);
            if ($vf > $ts) {
                continue;
            }
            if ($best === null || $vf > new \DateTimeImmutable($best->validFrom)) {
                $best = $r;
            }
        }

        return $best;
    }

    /** @return StoredPriceRow[] */
    public function allPriceRowsFor(string $providerCode, string $modelCode): array
    {
        $out = [];
        foreach ($this->rows as $r) {
            if ($r->provider === $providerCode && $r->model === $modelCode) {
                $out[] = $r;
            }
        }

        return $out;
    }

    /** @return string[] */
    public function providerModels(string $providerCode): array
    {
        $out = [];
        foreach ($this->rows as $r) {
            if ($r->provider === $providerCode) {
                $out[] = (string) $r->model;
            }
        }

        return array_values(array_unique($out));
    }
}
