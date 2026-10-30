<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\CostCalculator;
use App\Domain\MoneyMath;
use App\Ingest\IngestError;
use App\Ingest\Defs\PriceDef;
use Doctrine\DBAL\Connection;

/**
 * Ретро-коррекция цены (бонус): применяет исправленные строки цен (upsert по
 * (provider, model, valid_from)) и пересчитывает только те рассчитанные события,
 * для которых применимой стала заменённая строка.
 */
final class RepriceService
{
    public function __construct(
        private readonly Connection $db,
        private readonly CatalogService $catalog,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * @param PriceDef[] $prices
     * @return array{events: int, delta: string} delta — формат 6 знаков
     */
    public function apply(array $prices): array
    {
        return $this->db->transactional(function () use ($prices): array {
            // Провайдер/model из репрайса должны существовать в базе;
            // upsert сохраняет id строки — события, сославшиеся на неё, остаются связанными.
            $this->catalog->upsertCatalog(['providers' => [], 'models' => [], 'prices' => $prices]);

            $touchedIds = [];
            $pairs = [];
            foreach ($prices as $p) {
                $rowId = (int) $this->db->fetchOne(
                    'SELECT id FROM price_rows WHERE provider_code = ? AND model_code = ? AND valid_from = ?',
                    [$p->providerCode, $p->modelCode, $p->validFromUtc->format('Y-m-d H:i:s')]
                );
                if ($rowId === 0) {
                    throw new IngestError(sprintf('Внутренняя ошибка: строка цены %s/%s не найдена после upsert', $p->providerCode, $p->modelCode));
                }
                $touchedIds[$rowId] = true;
                $pairs[$p->providerCode][$p->modelCode] = true;
            }

            $count = 0;
            $delta = '0';
            foreach ($pairs as $providerCode => $models) {
                foreach ($models as $modelCode => $_) {
                    [$pairCount, $pairDelta] = $this->recomputePair($providerCode, $modelCode, $touchedIds);
                    $count += $pairCount;
                    $delta = MoneyMath::add($delta, $pairDelta);
                }
            }

            return ['events' => $count, 'delta' => MoneyMath::format6($delta)];
        });
    }

    /** @param array<int,bool> $touchedIds @return array{0: int, 1: string} */
    private function recomputePair(string $providerCode, string $modelCode, array $touchedIds): array
    {
        $rows = $this->db->fetchAllAssociative(
            'SELECT id, unit, rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any, valid_from
             FROM price_rows WHERE provider_code = ? AND model_code = ? ORDER BY valid_from ASC, id ASC',
            [$providerCode, $modelCode]
        );
        if ($rows === []) {
            return [0, '0'];
        }

        $events = $this->db->fetchAllAssociative(
            "SELECT id, ts, tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, cost_usd
             FROM usage_events WHERE provider_code = ? AND model_code = ? AND status = 'rated'",
            [$providerCode, $modelCode]
        );

        $provider = $this->db->fetchAssociative(
            'SELECT off_peak_window_start, off_peak_window_end, off_peak_multiplier FROM providers WHERE code = ?',
            [$providerCode]
        );
        $offPeak = ($provider === false || $provider['off_peak_multiplier'] === null) ? null : [
            'start' => (string) $provider['off_peak_window_start'],
            'end' => (string) $provider['off_peak_window_end'],
            'multiplier' => (string) $provider['off_peak_multiplier'],
        ];

        $count = 0;
        $delta = '0';

        foreach ($events as $event) {
            $ts = (string) $event['ts'];

            $applicable = null;
            foreach ($rows as $row) {
                if ($row['valid_from'] <= $ts) {
                    $applicable = $row;
                }
            }
            if ($applicable === null || !isset($touchedIds[(int) $applicable['id']])) {
                continue;
            }

            $newCost = $this->calculator->cost(
                (int) $event['tokens_input'],
                (int) $event['tokens_output'],
                (int) $event['tokens_cache_read'],
                (int) $event['tokens_cache_write'],
                [
                    'unit' => $applicable['unit'],
                    'input' => (string) ($applicable['rate_input'] ?? '0'),
                    'output' => (string) ($applicable['rate_output'] ?? '0'),
                    'cache_read' => (string) ($applicable['rate_cache_read'] ?? '0'),
                    'cache_write' => (string) ($applicable['rate_cache_write'] ?? '0'),
                    'any' => (string) ($applicable['rate_any'] ?? '0'),
                ],
                $offPeak,
                new \DateTimeImmutable($ts.' UTC'),
            );

            $oldCost = (string) ($event['cost_usd'] ?? '0');
            $delta = MoneyMath::add($delta, MoneyMath::sub($newCost, $oldCost));

            if (MoneyMath::format6($newCost) !== MoneyMath::format6($oldCost)) {
                $this->db->update(
                    'usage_events',
                    ['cost_usd' => MoneyMath::format6($newCost), 'price_row_id' => (int) $applicable['id']],
                    ['id' => (int) $event['id']]
                );
            }

            $count++;
        }

        return [$count, $delta];
    }
}
