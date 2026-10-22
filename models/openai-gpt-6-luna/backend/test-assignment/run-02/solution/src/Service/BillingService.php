<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;

final class BillingService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly CostCalculator $calculator,
        private readonly PriceImporter $priceImporter,
    ) {
    }

    /** @return array{rated: int, unrated: list<array{provider: string, request_id: string, ts: string}>} */
    public function rate(): array
    {
        $this->connection->beginTransaction();
        $rated = 0;
        $unrated = [];
        try {
            $events = $this->connection->fetchAllAssociative('SELECT * FROM usage_events WHERE cost_usd IS NULL ORDER BY id FOR UPDATE');
            foreach ($events as $event) {
                $price = $this->findPrice($event);
                if ($price === null) {
                    $unrated[] = [
                        'provider' => (string) $event['provider_code'],
                        'request_id' => (string) $event['provider_request_id'],
                        'ts' => $this->isoTimestamp($event['occurred_at']),
                    ];
                    continue;
                }
                $provider = $this->connection->fetchAssociative('SELECT * FROM providers WHERE code = :code', ['code' => $event['provider_code']]);
                if ($provider === false) {
                    $unrated[] = [
                        'provider' => (string) $event['provider_code'],
                        'request_id' => (string) $event['provider_request_id'],
                        'ts' => $this->isoTimestamp($event['occurred_at']),
                    ];
                    continue;
                }
                $cost = $this->calculator->calculate($event, $price, $provider);
                $this->connection->executeStatement(<<<'SQL'
UPDATE usage_events SET cost_usd = :cost, price_valid_from = :price_valid_from WHERE id = :id
SQL, ['cost' => $cost, 'price_valid_from' => $price['valid_from'], 'id' => $event['id']]);
                ++$rated;
            }
            $this->connection->commit();
            return ['rated' => $rated, 'unrated' => $unrated];
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /** @return array{count: int, delta: string} */
    public function reprice(string $file): array
    {
        $contents = @file_get_contents($file);
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Не удалось прочитать файл "%s"', $file));
        }
        $document = InputData::decodeObject($contents, $file);
        $prices = $document['prices'] ?? null;
        if (!is_array($prices) || !array_is_list($prices)) {
            throw new InvalidArgumentException(sprintf('%s: поле "prices" должно быть JSON-массивом', $file));
        }

        $this->connection->beginTransaction();
        $changed = [];
        try {
            foreach ($prices as $index => $record) {
                if (!is_array($record)) {
                    throw new InvalidArgumentException(sprintf('%s prices[%d]: ожидается объект', $file, $index));
                }
                $price = $this->priceImporter->parsePrice($record, sprintf('%s prices[%d]', $file, $index));
                if (!$this->connection->fetchOne('SELECT 1 FROM providers WHERE code = :code', ['code' => $price['provider']])) {
                    throw new InvalidArgumentException(sprintf('%s prices[%d]: провайдер "%s" отсутствует в базе', $file, $index, $price['provider']));
                }
                if (!$this->connection->fetchOne('SELECT 1 FROM provider_models WHERE provider_code = :provider AND code = :model', ['provider' => $price['provider'], 'model' => $price['model']])) {
                    throw new InvalidArgumentException(sprintf('%s prices[%d]: модель "%s/%s" отсутствует в базе', $file, $index, $price['provider'], $price['model']));
                }

                $old = $this->connection->fetchAssociative(<<<'SQL'
SELECT currency, unit, rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any
FROM prices WHERE provider_code = :provider AND model_code = :model AND valid_from = :valid_from
SQL, ['provider' => $price['provider'], 'model' => $price['model'], 'valid_from' => $price['valid_from']]);
                if ($old === false || !$this->samePrice($old, $price)) {
                    $this->connection->executeStatement(<<<'SQL'
INSERT INTO prices (
    provider_code, model_code, valid_from, currency, unit,
    rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any
) VALUES (
    :provider, :model, :valid_from, :currency, :unit,
    :rate_input, :rate_output, :rate_cache_read, :rate_cache_write, :rate_any
)
ON CONFLICT (provider_code, model_code, valid_from) DO UPDATE SET
    currency = EXCLUDED.currency, unit = EXCLUDED.unit,
    rate_input = EXCLUDED.rate_input, rate_output = EXCLUDED.rate_output,
    rate_cache_read = EXCLUDED.rate_cache_read, rate_cache_write = EXCLUDED.rate_cache_write,
    rate_any = EXCLUDED.rate_any
SQL, $price);
                    $changed[] = $price;
                }
            }

            $count = 0;
            $delta = '0.000000';
            foreach ($changed as $price) {
                $events = $this->connection->fetchAllAssociative(<<<'SQL'
SELECT * FROM usage_events
WHERE provider_code = :provider AND model_code = :model AND price_valid_from = :valid_from
ORDER BY id FOR UPDATE
SQL, ['provider' => $price['provider'], 'model' => $price['model'], 'valid_from' => $price['valid_from']]);
                $provider = $this->connection->fetchAssociative('SELECT * FROM providers WHERE code = :code', ['code' => $price['provider']]);
                if ($provider === false) {
                    throw new InvalidArgumentException(sprintf('Провайдер "%s" отсутствует в базе', $price['provider']));
                }
                foreach ($events as $event) {
                    $newCost = $this->calculator->calculate($event, $price, $provider);
                    $delta = bcadd($delta, bcsub($newCost, (string) $event['cost_usd'], 6), 6);
                    $this->connection->executeStatement('UPDATE usage_events SET cost_usd = :cost WHERE id = :id', [
                        'cost' => $newCost,
                        'id' => $event['id'],
                    ]);
                    ++$count;
                }
            }
            $this->connection->commit();
            return ['count' => $count, 'delta' => $delta];
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /** @param array<string, mixed> $event
     *  @return array<string, mixed>|null
     */
    private function findPrice(array $event): ?array
    {
        $price = $this->connection->fetchAssociative(<<<'SQL'
SELECT * FROM prices
WHERE provider_code = :provider AND model_code = :model AND valid_from <= :occurred_at
ORDER BY valid_from DESC LIMIT 1
SQL, ['provider' => $event['provider_code'], 'model' => $event['model_code'], 'occurred_at' => $event['occurred_at']]);

        return $price === false ? null : $price;
    }

    /** @param array<string, mixed> $old
     *  @param array<string, mixed> $new
     */
    private function samePrice(array $old, array $new): bool
    {
        foreach (['currency', 'unit'] as $field) {
            if ($old[$field] !== $new[$field]) {
                return false;
            }
        }
        foreach (['rate_input', 'rate_output', 'rate_cache_read', 'rate_cache_write', 'rate_any'] as $field) {
            if ($old[$field] === null || $new[$field] === null) {
                if ($old[$field] !== $new[$field]) {
                    return false;
                }
            } elseif (bccomp((string) $old[$field], (string) $new[$field], 6) !== 0) {
                return false;
            }
        }
        return true;
    }

    private function isoTimestamp(mixed $timestamp): string
    {
        $date = (new \DateTimeImmutable((string) $timestamp))->setTimezone(new \DateTimeZone('UTC'));
        $fraction = rtrim($date->format('u'), '0');
        return $date->format('Y-m-d\TH:i:s').($fraction === '' ? '' : '.'.$fraction).'Z';
    }
}
