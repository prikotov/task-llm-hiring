<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Exception\ImportException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём справочника цен: идемпотентный upsert провайдеров, моделей и строк цен.
 *
 *  - провайдеры   — по code;
 *  - модели       — по (provider, code);
 *  - строки цен   — по (provider, model, valid_from).
 *
 * Повторная загрузка тех же данных не изменяет содержимое БД.
 */
final class PriceCatalogImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function import(string $path): void
    {
        $data = $this->parse($path);

        $this->em->wrapInTransaction(function () use ($data): void {
            foreach ($data['providers'] as $p) {
                $this->upsertProvider($p['code'], $p['name'] ?? $p['code'], $p['off_peak'] ?? null);
            }
            // Флашим провайдеров ДО моделей/цен: ensureProviderCode сверяется с БД
            // (SELECT), и пока запись ещё только в identity-map — она не видна.
            $this->em->flush();

            foreach ($data['models'] as $m) {
                $this->ensureProviderCode($m['provider']);
                $this->ensureModel($m['provider'], $m['code']);
            }
            $this->em->flush();

            foreach ($data['prices'] as $pr) {
                $this->upsertPrice($pr);
            }
            $this->em->flush();
        });
    }

    /**
     * @param array<string,mixed> $raw
     * @return array{providers: array<int,array<string,mixed>>, models: array<int,array<string,mixed>>, prices: array<int,array<string,mixed>>}
     */
    private function parse(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл цен не найден или недоступен: %s', $path));
        }
        $json = file_get_contents($path);
        try {
            $data = json_decode($json === false ? '' : $json, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('Файл цен содержит некорректный JSON: %s', $e->getMessage()));
        }
        if (!is_array($data)) {
            throw new ImportException('Справочник цен должен быть объектом JSON с ключами providers/models/prices');
        }

        $providers = is_array($data['providers'] ?? null) ? $data['providers'] : [];
        $models = is_array($data['models'] ?? null) ? $data['models'] : [];
        $prices = is_array($data['prices'] ?? null) ? $data['prices'] : [];
        if (!is_array($providers) || !is_array($models) || !is_array($prices)) {
            throw new ImportException('Секции providers/models/prices должны быть массивами');
        }

        foreach ($providers as $i => $p) {
            $this->requireString($p['code'] ?? null, sprintf('providers[%d].code', $i));
            if (isset($p['name']) && !is_string($p['name'])) {
                throw new ImportException(sprintf('providers[%d].name должен быть строкой', $i));
            }
            $this->parseProviderOffPeak($p, $i);
        }

        foreach ($models as $i => $m) {
            $this->requireString($m['provider'] ?? null, sprintf('models[%d].provider', $i));
            $this->requireString($m['code'] ?? null, sprintf('models[%d].code', $i));
        }

        foreach ($prices as $i => $p) {
            $this->requireString($p['provider'] ?? null, sprintf('prices[%d].provider', $i));
            $this->requireString($p['model'] ?? null, sprintf('prices[%d].model', $i));
            $this->requireString($p['valid_from'] ?? null, sprintf('prices[%d].valid_from', $i));
            self::parseInstant($p['valid_from']);
            $this->requireString($p['unit'] ?? null, sprintf('prices[%d].unit', $i));
            if (!in_array($p['unit'], [PriceRow::UNIT_PER_TOKEN, PriceRow::UNIT_UNIFIED], true)) {
                throw new ImportException(sprintf('prices[%d].unit: неизвестная схема "%s"', $i, (string) $p['unit']));
            }
            $rates = $p['rates'] ?? null;
            if (!is_array($rates) || $rates === []) {
                throw new ImportException(sprintf('prices[%d].rates должен быть непустым объектом', $i));
            }
            foreach ($rates as $key => $value) {
                if (!is_string($value) && !is_int($value)) {
                    throw new ImportException(sprintf('prices[%d].rates.%s должен быть десятичной строкой', $i, (string) $key));
                }
            }
        }

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    /**
     * @param array<string,mixed> $provider
     */
    private function parseProviderOffPeak(array $provider, int $index): void
    {
        $offPeak = $provider['off_peak'] ?? null;
        if ($offPeak === null) {
            return;
        }
        if (!is_array($offPeak)) {
            throw new ImportException(sprintf('providers[%d].off_peak должен быть объектом или null', $index));
        }
        $this->requireString($offPeak['window_start'] ?? null, sprintf('providers[%d].off_peak.window_start', $index));
        $this->requireString($offPeak['window_end'] ?? null, sprintf('providers[%d].off_peak.window_end', $index));
        if (isset($offPeak['multiplier']) && !is_string($offPeak['multiplier'])) {
            throw new ImportException(sprintf('providers[%d].off_peak.multiplier должна быть строкой', $index));
        }
    }

    private function requireString(mixed $value, string $field): void
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: обязательное строковое поле', $field));
        }
    }

    private function upsertProvider(string $code, string $name, mixed $offPeak): Provider
    {
        $entity = $this->em->find(Provider::class, $code) ?? new Provider($code, $name);
        $entity->setName($name);

        $start = $end = null;
        $multiplier = null;
        if (is_array($offPeak)) {
            $start = (string) ($offPeak['window_start'] ?? null);
            $end = (string) ($offPeak['window_end'] ?? null);
            $multiplier = isset($offPeak['multiplier']) ? (string) $offPeak['multiplier'] : null;
        }
        $entity->setOffPeak($start, $end, $multiplier);

        $this->em->persist($entity);

        return $entity;
    }

    /**
     * Гарантирует наличие провайдера (создаёт минимального, если его ссылались
     * модель/строка цены, но он не описан в секции providers).
     *
     * @return array<string,mixed>|null id провайдера
     */
    private function ensureProviderCode(string $code): void
    {
        if ($this->providerExists($code)) {
            return;
        }
        $this->upsertProvider($code, $code, null);
    }

    /**
     * @return array<string,mixed>
     */
    private function ensureModel(string $provider, string $code): array
    {
        $id = $this->findModelId($provider, $code);
        if ($id === null) {
            $this->em->persist(new Model($provider, $code));
            $this->em->flush();
            $id = $this->findModelId($provider, $code);
            if ($id === null) {
                throw new ImportException(sprintf('Не удалось создать модель %s/%s', $provider, $code));
            }
        }

        return ['model_id' => (int) $id];
    }

    /**
     * @param array<string,mixed> $price
     */
    private function upsertPrice(array $price): void
    {
        $this->ensureProviderCode($price['provider']);
        $model = $this->ensureModel($price['provider'], $price['model']);
        $validFrom = self::parseInstant($price['valid_from']);
        $currency = is_string($price['currency'] ?? null) ? $price['currency'] : 'USD';
        $rates = [];
        foreach ($price['rates'] as $key => $value) {
            $rates[(string) $key] = (string) $value;
        }

        $existingId = $this->findPriceRowId($model['model_id'], $validFrom);
        if ($existingId !== null) {
            /** @var PriceRow $row */
            $row = $this->em->find(PriceRow::class, $existingId);
            $row->setValidFrom($validFrom);
            $row->setCurrency($currency);
            $row->setUnit((string) $price['unit']);
            $row->setRates($rates);
        } else {
            $this->em->persist(new PriceRow(
                (int) $model['model_id'],
                $validFrom,
                $currency,
                (string) $price['unit'],
                $rates,
            ));
        }
    }

    private function providerExists(string $code): bool
    {
        $conn = $this->em->getConnection();
        return (bool) $conn->fetchOne(
            'SELECT 1 FROM providers WHERE code = :code',
            ['code' => $code],
        );
    }

    private function findModelId(string $provider, string $code): ?string
    {
        $conn = $this->em->getConnection();
        $row = $conn->fetchOne(
            'SELECT id FROM catalog_models WHERE provider = :provider AND code = :code',
            ['provider' => $provider, 'code' => $code],
        );

        return $row === null || $row === false ? null : (string) $row;
    }

    private function findPriceRowId(int $modelId, \DateTimeImmutable $validFrom): ?string
    {
        $conn = $this->em->getConnection();
        $row = $conn->fetchOne(
            'SELECT id FROM price_rows WHERE model_id = :m AND valid_from = :vf',
            ['m' => $modelId, 'vf' => \App\Support\SqlDateTime::utc($validFrom)],
        );

        return $row === null || $row === false ? null : (string) $row;
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
