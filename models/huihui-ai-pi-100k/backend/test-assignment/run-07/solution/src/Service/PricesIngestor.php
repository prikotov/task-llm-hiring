<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PricesDocument;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use App\Exception\DataImportException;

/**
 * Идемпотентная загрузка справочника цен:
 *  - провайдеры — upsert по `code`;
 *  - модели — upsert по `(provider, code)`;
 *  - строки цен — upsert по `(provider, model, valid_from)`.
 *
 * Повторная загрузка тех же сумм не изменяет данных (ON CONFLICT DO UPDATE
 * теми же значениями). Проверки ссылочной целостности: модель и строка цен
 * должны ссылаться на существующих провайдеров/модели (в базе после
 * upsert, из этого же файла).
 */
final class PricesIngestor
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function import(PricesDocument $doc): void
    {
        $this->connection->transactional(function () use ($doc): void {
            foreach ($doc->providers as $p) {
                $this->upsertProvider($p);
            }
            foreach ($doc->models as $m) {
                $this->assertProviderExists($m['provider']);
                $this->connection->executeStatement(
                    'INSERT INTO models (provider_code, code)
                     VALUES (:provider, :code)
                     ON CONFLICT (provider_code, code) DO NOTHING',
                    ['provider' => $m['provider'], 'code' => $m['code']]
                );
            }
            foreach ($doc->prices as $pr) {
                $this->assertModelExists($pr['provider'], $pr['model']);
                $this->upsertPriceRow($pr);
            }
        });
    }

    /**
     * @phpstan-param array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>} $row
     */
    public function upsertPriceRow(array $row): void
    {
        $this->connection->executeStatement(
            'INSERT INTO prices (provider_code, model_code, valid_from, currency, unit, rates)
             VALUES (:provider, :model, :valid_from, :currency, :unit, :rates)
             ON CONFLICT (provider_code, model_code, valid_from) DO UPDATE
               SET currency = EXCLUDED.currency,
                   unit = EXCLUDED.unit,
                   rates = EXCLUDED.rates',
            [
                'provider' => $row['provider'],
                'model' => $row['model'],
                'valid_from' => $row['valid_from'],
                'currency' => $row['currency'],
                'unit' => $row['unit'],
                'rates' => $row['rates'],
            ],
            ['valid_from' => Types::DATETIMETZ_IMMUTABLE, 'rates' => Types::JSONB]
        );
    }

    public function assertProviderExists(string $code): void
    {
        $found = $this->connection->fetchOne(
            'SELECT 1 FROM providers WHERE code = :code',
            ['code' => $code]
        );
        if ($found === false) {
            throw new DataImportException(sprintf('Неизвестный провайдер "%s"', $code));
        }
    }

    public function assertModelExists(string $provider, string $model): void
    {
        $found = $this->connection->fetchOne(
            'SELECT 1 FROM models WHERE provider_code = :p AND code = :m',
            ['p' => $provider, 'm' => $model]
        );
        if ($found === false) {
            throw new DataImportException(sprintf('Неизвестная модель "%s/%s"', $provider, $model));
        }
    }

    /**
     * @phpstan-param array{code: string, name: string, off_peak: array{window_start: string, window_end: string, multiplier: string}|null} $p
     */
    private function upsertProvider(array $p): void
    {
        $op = $p['off_peak'];
        $this->connection->executeStatement(
            'INSERT INTO providers (code, name, off_peak_window_start, off_peak_window_end, off_peak_multiplier)
             VALUES (:code, :name, :ws, :we, :mult)
             ON CONFLICT (code) DO UPDATE
               SET name = EXCLUDED.name,
                   off_peak_window_start = EXCLUDED.off_peak_window_start,
                   off_peak_window_end = EXCLUDED.off_peak_window_end,
                   off_peak_multiplier = EXCLUDED.off_peak_multiplier',
            [
                'code' => $p['code'],
                'name' => $p['name'],
                'ws' => $op['window_start'] ?? null,
                'we' => $op['window_end'] ?? null,
                'mult' => $op['multiplier'] ?? null,
            ]
        );
    }
}
