<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\DomainException;
use App\Domain\OffPeakWindow;
use App\Domain\PricingEngine;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Repository\ModelRepository;
use App\Repository\PriceRowRepository;
use App\Repository\ProviderRepository;
use App\Support\IsoTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт справочника (провайдеры, модели, история цен).
 * Идемпотентный upsert:
 *  - провайдеры  — по code;
 *  - модели      — по (provider, code);
 *  - строки цен  — по (provider, model, valid_from).
 */
final class PricesImporter
{
    /** @var list<array{provider: string, code: string}> */
    private array $fileModels = [];

    /** @var list<string> */
    private array $fileProviders = [];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRowRepository $prices,
    ) {
    }

    /**
     * @throws DomainException на некорректном файле
     *
     * @return \App\Entity\PriceRow[] строки цен, затронутые этим импортом
     */
    public function import(string $file, bool $repriceMode = false): array
    {
        $data = $this->readAndValidate($file, $repriceMode);
        $touched = [];

        $this->em->wrapInTransaction(function () use ($data, $repriceMode, &$touched): void {
            if (!$repriceMode) {
                foreach ($data['providers'] as $row) {
                    $this->upsertProvider($row);
                }
                foreach ($data['models'] as $row) {
                    $this->upsertModel($row['provider'], $row['code']);
                }
            }
            foreach ($data['prices'] as $row) {
                $touched[] = $this->upsertPrice($row);
            }
        });

        return $touched;
    }

    /**
     * @return array{
     *     providers: list<array{code: string, name: string, off_peak: ?array<string, mixed>}>|list<array<string, mixed>>,
     *     models: list<array{provider: string, code: string}>,
     *     prices: list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>}},
     * }
     */
    private function readAndValidate(string $file, bool $repriceMode): array
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new DomainException(sprintf('Файл "%s" не найден или недоступен', $file));
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new DomainException(sprintf('Не удалось прочитать файл "%s"', $file));
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new DomainException('Некорректный JSON');
        }

        if (isset($decoded['providers']) && !is_array($decoded['providers'])) {
            throw new DomainException('Секция "providers" должна быть массивом');
        }
        if (isset($decoded['models']) && !is_array($decoded['models'])) {
            throw new DomainException('Секция "models" должна быть массивом');
        }
        if (!isset($decoded['prices']) || !is_array($decoded['prices'])) {
            throw new DomainException('Секция "prices" обязательна и должна быть массивом');
        }

        // --- providers: в обычном режиме секция обязательна; в reprice-режиме
        // секция может отсутствовать — наличие проверяем по БД.
        $this->fileProviders = [];
        if ($repriceMode) {
            $this->fileProviders = array_map(static fn (Provider $p): string => $p->code, $this->providers->findAllActive());
        } else {
            if (!isset($decoded['providers']) || $decoded['providers'] === []) {
                throw new DomainException('Секция "providers" обязательна и не может быть пустой');
            }
            foreach ($decoded['providers'] as $i => $p) {
                $this->assertProviderRow($p, sprintf('providers[%d]', $i));
                $this->fileProviders[] = self::stringField($p, 'code', sprintf('providers[%d]', $i));
            }
        }

        // --- models: секция опциональна (может быть пустой).
        $this->fileModels = [];
        foreach ($decoded['models'] ?? [] as $i => $m) {
            $where = sprintf('models[%d]', $i);
            if (!is_array($m)) {
                throw new DomainException(sprintf('%s: объект обязателен', $where));
            }
            $entry = [
                'provider' => self::stringField($m, 'provider', $where),
                'code' => self::stringField($m, 'code', $where),
            ];
            if (!in_array($entry['provider'], $this->fileProviders, true)) {
                throw new DomainException(sprintf('%s: неизвестный провайдер "%s"', $where, $entry['provider']));
            }
            $this->fileModels[] = $entry;
        }
        if (!$repriceMode && $this->fileModels === []) {
            throw new DomainException('Секция "models" обязательна и не может быть пустой');
        }

        // --- prices.
        $prices = [];
        foreach ($decoded['prices'] as $i => $row) {
            $where = sprintf('prices[%d]', $i);
            if (!is_array($row)) {
                throw new DomainException(sprintf('%s: объект обязателен', $where));
            }

            $provider = self::stringField($row, 'provider', $where);
            $model = self::stringField($row, 'model', $where);
            $validFrom = IsoTime::parseUtc(self::stringField($row, 'valid_from', $where), $where);
            $currency = self::stringField($row, 'currency', $where);
            $unit = self::stringField($row, 'unit', $where);

            $rates = $row['rates'] ?? null;
            if (!is_array($rates) || $rates === []) {
                throw new DomainException(sprintf('%s: rates — объект «вид → ставка»', $where));
            }
            foreach ($rates as $rate) {
                if (!is_string($rate) || !preg_match('/^\d+(\.\d{1,6})?$/', $rate)) {
                    throw new DomainException(sprintf('%s: ставка — десятичная строка (целые/6 знаков после точки)', $where));
                }
            }
            PricingEngine::assertRatesConsistent($unit, $rates);

            if ($repriceMode) {
                if (!in_array($provider, $this->fileProviders, true)) {
                    throw new DomainException(sprintf('%s: провайдер "%s" отсутствует в базе', $where, $provider));
                }
                if ($this->models->findByRef($provider, $model) === null) {
                    throw new DomainException(sprintf('%s: модель "%s/%s" отсутствует в базе', $where, $provider, $model));
                }
            } else {
                if (!in_array($provider, $this->fileProviders, true)) {
                    throw new DomainException(sprintf('%s: неизвестный провайдер "%s"', $where, $provider));
                }
                if (!in_array($this->modelRef($provider, $model), $this->fileModels, true)) {
                    throw new DomainException(sprintf('%s: неизвестная модель "%s/%s"', $where, $provider, $model));
                }
            }

            $prices[] = [
                'provider' => $provider,
                'model' => $model,
                'valid_from' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return [
            'providers' => $decoded['providers'] ?? [],
            'models' => $this->fileModels,
            'prices' => $prices,
        ];
    }

    private function assertProviderRow(mixed $row, string $where): void
    {
        if (!is_array($row)) {
            throw new DomainException(sprintf('%s: объект обязателен', $where));
        }
        self::stringField($row, 'code', $where);
        self::stringField($row, 'name', $where);

        $offPeak = $row['off_peak'] ?? null;
        if ($offPeak === null) {
            return;
        }
        if (!is_array($offPeak)) {
            throw new DomainException(sprintf('%s: off_peak — объект или null', $where));
        }
        $start = self::stringField($offPeak, 'window_start', "$where.off_peak");
        $end = self::stringField($offPeak, 'window_end', "$where.off_peak");
        $multiplier = self::stringField($offPeak, 'multiplier', "$where.off_peak");
        if (!preg_match('/^\d+(\.\d{1,6})?$/', $multiplier)) {
            throw new DomainException(sprintf('%s.off_peak: multiplier — десятичная строка', $where));
        }
        OffPeakWindow::parse($start, $end); // валидирует HH:MM
    }

    private function upsertProvider(array $row): void
    {
        $code = $row['code'];
        $provider = $this->providers->findByCode($code) ?? new Provider();
        $provider->code = $code;
        $provider->name = $row['name'];
        $provider->offPeakStart = null;
        $provider->offPeakEnd = null;
        $provider->offPeakMultiplier = null;

        if (($row['off_peak'] ?? null) !== null) {
            $provider->offPeakStart = $row['off_peak']['window_start'];
            $provider->offPeakEnd = $row['off_peak']['window_end'];
            $provider->offPeakMultiplier = $row['off_peak']['multiplier'];
        }

        $this->em->persist($provider);
    }

    private function upsertModel(string $provider, string $code): void
    {
        $model = $this->models->findByRef($provider, $code) ?? new Model();
        $model->provider = $provider;
        $model->code = $code;
        $this->em->persist($model);
    }

    /** @param array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>} $row */
    private function upsertPrice(array $row): PriceRow
    {
        $existing = $this->prices->findByRef($row['provider'], $row['model'], $row['valid_from']);
        if ($existing === null) {
            $existing = new PriceRow(
                $row['provider'],
                $row['model'],
                $row['valid_from'],
                $row['currency'],
                $row['unit'],
                $row['rates'],
            );
            $this->em->persist($existing);
        } else {
            $existing->currency = $row['currency'];
            $existing->unit = $row['unit'];
            $existing->ratesJson = json_encode($row['rates'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }

        return $existing;
    }

    private function modelRef(string $provider, string $code): array
    {
        return ['provider' => $provider, 'code' => $code];
    }

    private static function stringField(array $row, string $field, string $where): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new DomainException(sprintf('%s: поле "%s" — непустая строка', $where, $field));
        }

        return $value;
    }
}
