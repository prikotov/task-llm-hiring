<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Catalog\PriceRow;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Справочник цен в памяти на время прогона: выбор «действовавшей» строки цены.
 *
 * Строка цены события — строка с наибольшим valid_from, ≤ ts события
 * (valid_from включительно).
 */
final class PriceCatalog
{
    /** @var array<string, list<PriceRow>> ключ "provider|model" → строки, отсортированные по validFrom */
    private array $pricesByModel = [];

    /** @var array<string, ?\App\Domain\ValueObject\OffPeak> code → настройка off-peak провайдера */
    private array $offPeakByProvider = [];

    private bool $loaded = false;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }

        foreach ($this->em->getRepository(PriceRow::class)->findAll() as $row) {
            $key = $row->provider().'|'.$row->model();
            $this->pricesByModel[$key][] = $row;
        }

        foreach ($this->pricesByModel as $key => $rows) {
            usort($rows, static fn (PriceRow $a, PriceRow $b) => $a->validFrom() <=> $b->validFrom());
            $this->pricesByModel[$key] = $rows;
        }

        $providers = $this->em->getConnection()->executeQuery('SELECT code, off_peak_start, off_peak_end, off_peak_multiplier FROM providers')->fetchAllAssociative();
        foreach ($providers as $p) {
            $this->offPeakByProvider[$p['code']] = $p['off_peak_start'] === null
                ? null
                : new \App\Domain\ValueObject\OffPeak($p['off_peak_start'], $p['off_peak_end'], $p['off_peak_multiplier']);
        }

        $this->loaded = true;
    }

    /** Строка цены, действовавшая в момент ts, или null (событие unrated). */
    public function findPrice(string $provider, string $model, \DateTimeImmutable $ts): ?PriceRow
    {
        $this->load();

        return self::selectFrom($this->pricesByModel["$provider|$model"] ?? [], $ts);
    }

    /**
     * Правило выбора цены: строка с наибольшим valid_from ≤ ts
     * (valid_from включительно). $rows — отсортированы по validFrom по
     * возрастанию; возвращается null, если такой строки нет.
     *
     * @param list<PriceRow> $rows
     */
    public static function selectFrom(array $rows, \DateTimeImmutable $ts): ?PriceRow
    {
        $found = null;
        foreach ($rows as $row) {
            if ($row->validFrom() > $ts) {
                break;
            }
            $found = $row;
        }

        return $found;
    }

    public function offPeak(string $provider): ?\App\Domain\ValueObject\OffPeak
    {
        $this->load();

        return $this->offPeakByProvider["$provider"] ?? null;
    }
}
