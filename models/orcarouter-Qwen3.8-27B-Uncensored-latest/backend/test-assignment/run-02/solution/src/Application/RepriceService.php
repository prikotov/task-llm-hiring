<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Decimal;
use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use App\Exception\ImportException;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;

/**
 * Ретро-коррекция цены (app:billing:reprice, бонус).
 *
 * Механизм: исправленные строки upsert'ятся по (provider, model, valid_from);
 * провайдер и модель обязаны существовать в уже загруженном справочнике (БД),
 * а не обязательно присутствовать в самом файле коррекции. Далее уже
 * рассчитанные события, тарифицировавшиеся именно этой строкой
 * (usage_events.price_line_id), пересчитываются по новым ставкам; каждая
 * правка фиксируется в журнале reprice_corrections (старая и новая стоимость).
 * События, рассчитанные по *другим* строкам цен, не трогаются.
 * Новые строки для ранее unrated-событий подхватываются app:billing:rate.
 */
final class RepriceService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RatesCalculator $calculator,
    ) {
    }

    /**
     * @throws ImportException
     * @throws DbalException
     *
     * @return array{events: int, delta: string}
     */
    public function reprice(string $file): array
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new ImportException(sprintf('file not found: %s', $file));
        }

        $data = PriceFileParser::parse(file_get_contents($file), PriceFileParser::MODE_REPRICE);

        $conn = $this->connection;

        return $conn->transactional(function () use ($conn, $data) {
            $touchedLineIds = [];
            foreach ($data['prices'] as $price) {
                $this->assertReferenceExists($conn, $price['provider'], $price['model']);
                $touchedLineIds[] = $this->upsertPriceLine($conn, $price);
            }

            $touchedLineIds = array_values(array_unique($touchedLineIds));
            $placeholders = implode(',', array_fill(0, \count($touchedLineIds), '?'));

            $events = $conn->fetchAllAssociative(
                "SELECT e.id,
                        e.tokens_input,
                        e.tokens_output,
                        e.tokens_cache_read,
                        e.tokens_cache_write,
                        e.ts,
                        e.cost,
                        e.provider_code,
                        l.unit,
                        l.rates,
                        p.off_peak
                 FROM usage_events e
                 INNER JOIN price_lines l ON l.id = e.price_line_id
                 LEFT JOIN providers p ON p.code = e.provider_code
                 WHERE e.status = 'rated' AND l.id IN ($placeholders)
                 ORDER BY e.id",
                $touchedLineIds,
            );

            $delta = '0';
            foreach ($events as $e) {
                $window = $e['off_peak'] !== null
                    ? OffPeakWindow::decode((array) json_decode($e['off_peak'], true))
                    : null;
                $usage = new TokenUsage(
                    (int) $e['tokens_input'],
                    (int) $e['tokens_output'],
                    (int) $e['tokens_cache_read'],
                    (int) $e['tokens_cache_write'],
                );
                $rates = json_decode($e['rates'], true, 512, \JSON_THROW_ON_ERROR);
                $newCost = $this->calculator->finalCost(
                    $usage,
                    $e['unit'],
                    $rates,
                    $window,
                    new DateTimeImmutable($e['ts'], new DateTimeZone('UTC')),
                );
                $oldCost = (string) $e['cost'];

                $delta = Decimal::add($delta, Decimal::sub($newCost, $oldCost));

                $conn->executeStatement(
                    'UPDATE usage_events SET cost = ? WHERE id = ?',
                    [$newCost, $e['id']],
                );
                $conn->executeStatement(
                    'INSERT INTO reprice_corrections (event_id, old_cost, new_cost, created_at)
                     VALUES (?, ?, ?, NOW())',
                    [$e['id'], $oldCost, $newCost],
                );
            }

            return ['events' => \count($events), 'delta' => Decimal::roundHalfUp($delta, 6)];
        });
    }

    private function assertReferenceExists(Connection $conn, string $provider, string $model): void
    {
        $providerOk = (bool) $conn->fetchOne('SELECT 1 FROM providers WHERE code = ?', [$provider]);
        $modelOk = (bool) $conn->fetchOne(
            'SELECT 1 FROM models WHERE provider_code = ? AND code = ?',
            [$provider, $model],
        );
        if (!$providerOk || !$modelOk) {
            throw new ImportException(sprintf(
                'reprice: provider "%s" / model "%s" must exist in the loaded reference data',
                $provider,
                $model,
            ));
        }
    }

    /**
     * @param array{provider: string, model: string, valid_from: \DateTimeImmutable,
     *              currency: string, unit: string, rates: array<string,string>} $price
     */
    private function upsertPriceLine(Connection $conn, array $price): int
    {
        $rates = json_encode($price['rates'], \JSON_THROW_ON_ERROR);
        $existing = $conn->fetchAssociative(
            'SELECT id FROM price_lines
             WHERE provider_code = ? AND model_code = ? AND valid_from = ?',
            [$price['provider'], $price['model'], $price['valid_from']],
        );

        if ($existing) {
            $id = (int) $existing['id'];
            $conn->executeStatement(
                'UPDATE price_lines SET unit = ?, currency = ?, rates = ? WHERE id = ?',
                [$price['unit'], $price['currency'], $rates, $id],
            );

            return $id;
        }

        $conn->executeStatement(
            'INSERT INTO price_lines (provider_code, model_code, valid_from, currency, unit, rates)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $price['provider'],
                $price['model'],
                $price['valid_from'],
                $price['currency'],
                $price['unit'],
                $rates,
            ],
        );

        return (int) $conn->lastInsertId();
    }
}
