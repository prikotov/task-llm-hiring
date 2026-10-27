<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PricesDocument;
use App\Domain\Money;
use App\Domain\OffPeak;
use App\Domain\RateCalculator;
use App\Exception\DataImportException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

/**
 * Ретро-коррекция цены (бонус): провайдер прислал исправленную строку цен
 * (файл того же формата; секции `providers`/`models` могут отсутствовать
 * или быть пустыми — провайдер и модель должны существовать в уже
 * загруженном справочнике).
 *
 * Механизм — «перезапись с журналом корректировок»: строка цен
 * upsert'ится по `(provider, model, valid_from)`; уже рассчитанные
 * события, тарифицировавшиеся именно ею (`price_valid_from = valid_from`),
 * пересчитываются; старые/новые стоимости и дельта пишутся в реестр
 * `reprice_log`. Итоговый отчёт показывает только новые суммы, без задвоений.
 */
final class RepriceService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RateCalculator $calculator,
        private readonly PricesIngestor $pricesIngestor,
    ) {
    }

    /**
     * @return array{events: int, delta: string}
     */
    public function reprice(PricesDocument $doc): array
    {
        $count = 0;
        $delta = '0';

        $this->connection->transactional(function () use ($doc, &$count, &$delta): void {
            $keys = [];
            foreach ($doc->prices as $i => $row) {
                $this->pricesIngestor->assertProviderExists($row['provider']);
                $this->pricesIngestor->assertModelExists($row['provider'], $row['model']);

                // Одинаковый ключ несколько раз в одном файле — пересчитываем один раз.
                $key = $row['provider']
                    . "\x1F" . $row['model']
                    . "\x1F" . $row['valid_from']->format('Y-m-d H:i:s.u');
                $keys[$key] = $row;
            }

            foreach ($keys as $row) {
                $result = $this->repriceKey($row);
                $count += $result['count'];
                $delta = Money::add($delta, $result['delta']);
            }
        });

        return ['count' => $count, 'delta' => $delta];
    }

    /**
     * @param array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>} $row
     *
     * @return array{count: int, delta: string}
     */
    private function repriceKey(array $row): array
    {
        $this->pricesIngestor->upsertPriceRow($row);

        $events = $this->connection->fetchAllAssociative(
            'SELECT provider_request_id, ts,
                    tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
                    cost_usd
             FROM usage_events
             WHERE provider_code = :provider
               AND model_code = :model
               AND status = \'rated\'
               AND price_valid_from = :valid_from
             ORDER BY provider_request_id',
            [
                'provider' => $row['provider'],
                'model' => $row['model'],
                'valid_from' => $row['valid_from'],
            ],
            ['valid_from' => Types::DATETIMETZ_IMMUTABLE]
        );

        $offPeak = $this->offPeakFor($row['provider']);
        $count = 0;
        $delta = '0';

        foreach ($events as $e) {
            $ts = new \DateTimeImmutable($e['ts'], new \DateTimeZone('UTC'));
            $oldCost = (string) $e['cost_usd'];

            $newCost = $this->calculator->costUsd(
                ['unit' => $row['unit'], 'rates' => $row['rates']],
                [
                    'input' => (int) $e['tokens_input'],
                    'output' => (int) $e['tokens_output'],
                    'cache_read' => (int) $e['tokens_cache_read'],
                    'cache_write' => (int) $e['tokens_cache_write'],
                ],
                $offPeak,
                $ts
            );

            $delta = Money::add($delta, Money::sub($newCost, $oldCost));

            $this->connection->executeStatement(
                'UPDATE usage_events
                    SET cost_usd = :new_cost
                WHERE provider_code = :provider
                  AND provider_request_id = :request_id',
                [
                    'new_cost' => $newCost,
                    'provider' => $row['provider'],
                    'request_id' => $e['provider_request_id'],
                ]
            );

            $this->connection->executeStatement(
                "INSERT INTO reprice_log
                   (provider_code, provider_request_id, old_cost_usd, new_cost_usd, delta_usd, applied_at)
                 VALUES
                   (:provider, :request_id, :old_cost, :new_cost, :delta, NOW())",
                [
                    'provider' => $row['provider'],
                    'request_id' => $e['provider_request_id'],
                    'old_cost' => $oldCost,
                    'new_cost' => $newCost,
                    'delta' => Money::to6(Money::sub($newCost, $oldCost)),
                ]
            );

            $count += 1;
        }

        return ['count' => $count, 'delta' => $delta];
    }

    private function offPeakFor(string $provider): ?OffPeak
    {
        $row = $this->connection->fetchAssociative(
            'SELECT off_peak_window_start, off_peak_window_end, off_peak_multiplier
             FROM providers
             WHERE code = :code',
            ['code' => $provider]
        );
        if ($row === false) {
            return null;
        }

        return OffPeak::fromProvider(
            $row['off_peak_window_start'] === null ? null : (string) $row['off_peak_window_start'],
            $row['off_peak_window_end'] === null ? null : (string) $row['off_peak_window_end'],
            $row['off_peak_multiplier'] === null ? null : (string) $row['off_peak_multiplier']
        );
    }
}
