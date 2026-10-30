<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ImportException;
use App\Support\Decimal;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Провайдер присылает исправленную строку цен (формат как у справочника, но
 * секции providers/models могут отсутствовать). Механизм:
 *
 *  1) upsert строки цен по (provider, model, valid_from) уже загруженного
 *     справочника — провайдер и модель обязаны существовать в БД;
 *  2) пересчёт всех рассчитанных событий, тарифицированных заменёнными
 *     строками (e.price_row_id), по актуальным ставкам + оф-пику провайдера;
 *  3) новая стоимость запирается в событии, старая и новая — в журнале
 *     reprice_entries (аудит без задвоений).
 *
 * Итоговый app:billing:report после reprice показывает пересчитанные суммы.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingEngine $engine,
    ) {
    }

    /**
     * @return array{events: int, delta: string}
     */
    public function reprice(string $path): array
    {
        $prices = $this->parseCorrection($path);

        return $this->em->wrapInTransaction(function () use ($prices): array {
            $updatedRowIds = $this->upsertPriceRows($prices);

            if ($updatedRowIds === []) {
                return ['events' => 0, 'delta' => '0.000000'];
            }

            $conn = $this->em->getConnection();
            $events = $this->selectRatedEventsUsing($conn, $updatedRowIds);

            $sumNew = '0';
            $sumOld = '0';
            $count = 0;
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

            foreach ($events as $event) {
                $eventTs = new \DateTimeImmutable((string) $event['ts'], new \DateTimeZone('UTC'));
                $newCost = $this->engine->cost(
                    (string) $event['unit'],
                    json_decode((string) $event['rates'], true) ?? [],
                    (int) $event['tokens_input'],
                    (int) $event['tokens_output'],
                    (int) $event['tokens_cache_read'],
                    (int) $event['tokens_cache_write'],
                    isset($event['off_peak_window_start']) ? (string) $event['off_peak_window_start'] : null,
                    isset($event['off_peak_window_end']) ? (string) $event['off_peak_window_end'] : null,
                    isset($event['off_peak_multiplier']) ? (string) $event['off_peak_multiplier'] : null,
                    $eventTs,
                );

                $oldCost = Decimal::formatMoney((string) $event['old_cost']);
                $newCostFmt = $newCost;
                $sumNew = Decimal::add($sumNew, $newCostFmt);
                $sumOld = Decimal::add($sumOld, $oldCost);

                $conn->executeStatement(
                    "UPDATE usage_events SET cost_usd = :cost, price_row_id = :pid WHERE id = :id",
                    ['cost' => $newCostFmt, 'pid' => $event['price_row_id'], 'id' => $event['id']],
                );

                $this->em->persist(new \App\Entity\RepriceEntry(
                    (int) $event['id'],
                    (int) $event['price_row_id'],
                    $oldCost,
                    $newCostFmt,
                    $now,
                ));

                $count++;
            }

            $delta = \bcsub($sumNew, $sumOld, 30);
            $this->em->flush();

            return ['events' => $count, 'delta' => Decimal::formatMoney($delta)];
        });
    }

    /**
     * @param array<int,array<string,mixed>> $prices
     * @return int[] id заменённых строк цен
     */
    private function upsertPriceRows(array $prices): array
    {
        $ids = [];
        foreach ($prices as $price) {
            $ids[] = $this->upsertOne($price);
        }
        $this->em->flush();

        return $ids;
    }

    /**
     * @param array<string,mixed> $price
     */
    private function upsertOne(array $price): int
    {
        $provider = (string) $price['provider'];
        $model = (string) $price['model'];
        $validFrom = self::parseInstant((string) $price['valid_from']);

        $conn = $this->em->getConnection();
        $providerId = $conn->fetchOne('SELECT code FROM providers WHERE code = :c', ['c' => $provider]);
        if ($providerId === false || $providerId === null) {
            throw new ImportException(sprintf('Провайдер "%s" не найден в загрузленном справочнике (коррекция не создаёт новых провайдеров)', $provider));
        }
        $modelId = $conn->fetchOne(
            'SELECT id FROM catalog_models WHERE provider = :p AND code = :m',
            ['p' => $provider, 'm' => $model],
        );
        if ($modelId === false || $modelId === null) {
            throw new ImportException(sprintf('Модель %s/%s не найдена в справочнике', $provider, $model));
        }

        $rates = [];
        foreach ($price['rates'] as $key => $value) {
            $rates[(string) $key] = (string) $value;
        }
        $currency = is_string($price['currency'] ?? null) ? $price['currency'] : 'USD';

        $existingId = $conn->fetchOne(
            'SELECT id FROM price_rows WHERE model_id = :m AND valid_from = :vf',
            ['m' => $modelId, 'vf' => \App\Support\SqlDateTime::utc($validFrom)],
        );
        if ($existingId !== false && $existingId !== null) {
            /** @var \App\Entity\PriceRow $row */
            $row = $this->em->find(\App\Entity\PriceRow::class, (int) $existingId);
            $row->setValidFrom($validFrom);
            $row->setCurrency($currency);
            $row->setUnit((string) $price['unit']);
            $row->setRates($rates);

            return (int) $existingId;
        }

        $newRow = new \App\Entity\PriceRow((int) $modelId, $validFrom, $currency, (string) $price['unit'], $rates);
        $this->em->persist($newRow);
        $this->em->flush();
        $id = $newRow->getId();
        if ($id === null) {
            throw new ImportException('Не удалось создать строку цены при коррекции');
        }

        return $id;
    }

    /**
     * @param int[] $rowIds
     * @return array
     */
    private function selectRatedEventsUsing(Connection $conn, array $rowIds): array
    {
        $placeholders = implode(',', \array_fill(0, \count($rowIds), '?'));
        $sql = <<<SQL
            SELECT e.id, e.provider, e.provider_request_id, e.model_code, e.user_id, e.ts,
                   e.tokens_input, e.tokens_output, e.tokens_cache_read, e.tokens_cache_write,
                   e.cost_usd AS old_cost, pr.id AS price_row_id, pr.unit, pr.rates,
                   p.off_peak_window_start, p.off_peak_window_end, p.off_peak_multiplier
            FROM usage_events e
            JOIN price_rows pr ON pr.id = e.price_row_id
            JOIN providers p ON p.code = e.provider
            WHERE e.status = 'rated' AND e.price_row_id IN ({$placeholders})
            ORDER BY e.id
        SQL;

        return $conn->fetchAllAssociative($sql, $rowIds);
    }

    /**
     * Разбор файла коррекции: нужен только раздел prices; providers/models
     * могут отсутствовать или быть пустыми.
     *
     * @return array<int,array<string,mixed>>
     */
    private function parseCorrection(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл коррекции не найден или недоступен: %s', $path));
        }
        $json = file_get_contents($path);
        try {
            $data = json_decode($json === false ? '' : $json, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('Файл коррекции содержит некорректный JSON: %s', $e->getMessage()));
        }
        if (!is_array($data)) {
            throw new ImportException('Файл коррекции должен быть объектом JSON c разделом prices');
        }
        $prices = $data['prices'] ?? [];
        if (!is_array($prices)) {
            throw new ImportException('Раздел prices должен быть массивом');
        }

        foreach ($prices as $i => $p) {
            $this->requireString($p['provider'] ?? null, sprintf('prices[%d].provider', $i));
            $this->requireString($p['model'] ?? null, sprintf('prices[%d].model', $i));
            $this->requireString($p['valid_from'] ?? null, sprintf('prices[%d].valid_from', $i));
            self::parseInstant((string) $p['valid_from']);
            $this->requireString($p['unit'] ?? null, sprintf('prices[%d].unit', $i));
            $rates = $p['rates'] ?? null;
            if (!is_array($rates) || $rates === []) {
                throw new ImportException(sprintf('prices[%d].rates должен быть непустым объектом', $i));
            }
        }

        return $prices;
    }

    private function requireString(mixed $value, string $field): void
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: обязательное строковое поле', $field));
        }
    }

    private static function parseInstant(string $iso): \DateTimeImmutable
    {
        $trimmed = trim($iso);
        if ($trimmed === '') {
            throw new ImportException('Пустое значение времени');
        }
        try {
            $dt = new \DateTimeImmutable($trimmed, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException(sprintf('Некорректное время "%s"', $iso));
        }

        return $dt->setTimezone(new \DateTimeZone('UTC'));
    }
}
