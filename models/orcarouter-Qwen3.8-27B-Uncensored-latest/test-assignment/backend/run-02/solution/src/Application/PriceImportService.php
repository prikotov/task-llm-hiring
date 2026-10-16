<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\OffPeakWindow;
use App\Exception\ImportException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;

/**
 * Импорт справочника (app:prices:import): идемпотентный upsert
 * провайдеров, моделей и строк цен.
 */
final class PriceImportService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @throws ImportException
     * @throws DbalException
     */
    public function import(string $file, string $mode = PriceFileParser::MODE_IMPORT): void
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new ImportException(sprintf('file not found: %s', $file));
        }

        // @phpstan-ignore-next-line (file_get_contents: файл проверен выше)
        $data = PriceFileParser::parse(file_get_contents($file), $mode);

        $conn = $this->connection;
        $conn->transactional(function () use ($conn, $data) {
            foreach ($data['providers'] as $p) {
                $this->upsertProvider($conn, $p);
            }

            foreach ($data['models'] as $m) {
                $this->upsertModel($conn, $m);
            }

            foreach ($data['prices'] as $price) {
                $this->upsertPriceLine($conn, $price);
            }
        });
    }

    /** @param array{code: string, name: string, off_peak: OffPeakWindow|null} $p */
    private function upsertProvider(Connection $conn, array $p): void
    {
        $offPeak = $p['off_peak'] === null
            ? null
            : json_encode($p['off_peak']->encode(), \JSON_THROW_ON_ERROR);

        $existing = $conn->fetchAssociative('SELECT 1 AS x FROM providers WHERE code = ?', [$p['code']]);

        if ($existing) {
            $conn->executeStatement(
                'UPDATE providers SET name = ?, off_peak = ? WHERE code = ?',
                [$p['name'], $offPeak, $p['code']],
            );
        } else {
            $conn->executeStatement(
                'INSERT INTO providers (code, name, off_peak) VALUES (?, ?, ?)',
                [$p['code'], $p['name'], $offPeak],
            );
        }
    }

    /** @param array{provider: string, code: string} $m */
    private function upsertModel(Connection $conn, array $m): void
    {
        $providerExists = (bool) $conn->fetchOne('SELECT 1 FROM providers WHERE code = ?', [$m['provider']]);
        if (!$providerExists) {
            throw new ImportException(sprintf('models: provider "%s" not found', $m['provider']));
        }

        $exists = (bool) $conn->fetchOne(
            'SELECT 1 FROM models WHERE provider_code = ? AND code = ?',
            [$m['provider'], $m['code']],
        );
        if (!$exists) {
            $conn->executeStatement(
                'INSERT INTO models (provider_code, code) VALUES (?, ?)',
                [$m['provider'], $m['code']],
            );
        }
    }

    /**
     * @param array{provider: string, model: string, valid_from: \DateTimeImmutable,
     *              currency: string, unit: string, rates: array<string,string>} $price
     */
    private function upsertPriceLine(Connection $conn, array $price): void
    {
        $providerExists = (bool) $conn->fetchOne('SELECT 1 FROM providers WHERE code = ?', [$price['provider']]);
        $modelExists = (bool) $conn->fetchOne(
            'SELECT 1 FROM models WHERE provider_code = ? AND code = ?',
            [$price['provider'], $price['model']],
        );
        if (!$providerExists || !$modelExists) {
            throw new ImportException(sprintf(
                'prices: provider "%s" / model "%s" not found in reference data',
                $price['provider'],
                $price['model'],
            ));
        }

        $rates = json_encode($price['rates'], \JSON_THROW_ON_ERROR);
        $validFrom = $price['valid_from'];

        $existing = $conn->fetchAssociative(
            'SELECT id FROM price_lines WHERE provider_code = ? AND model_code = ? AND valid_from = ?',
            [$price['provider'], $price['model'], $validFrom],
        );

        if ($existing) {
            $conn->executeStatement(
                'UPDATE price_lines SET unit = ?, currency = ?, rates = ?
                 WHERE provider_code = ? AND model_code = ? AND valid_from = ?',
                [
                    $price['unit'],
                    $price['currency'],
                    $rates,
                    $price['provider'],
                    $price['model'],
                    $validFrom,
                ],
            );
        } else {
            $conn->executeStatement(
                'INSERT INTO price_lines (provider_code, model_code, valid_from, currency, unit, rates)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    $price['provider'],
                    $price['model'],
                    $validFrom,
                    $price['currency'],
                    $price['unit'],
                    $rates,
                ],
            );
        }
    }
}
