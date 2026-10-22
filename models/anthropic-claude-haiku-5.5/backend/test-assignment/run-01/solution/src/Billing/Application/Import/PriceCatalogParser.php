<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Domain\Catalog\OffPeakWindow;
use App\Billing\Domain\Catalog\PricingUnit;
use App\Billing\Domain\Shared\Time;
use Throwable;

/**
 * Разбор JSON-файла истории цен (и файла ретро-коррекции — та же структура,
 * но секции providers/models могут отсутствовать).
 */
final class PriceCatalogParser
{
    private const RATE_PATTERN = '/^\d+(\.\d{1,6})?$/';

    public function parseFile(string $path): PriceCatalogData
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidInputException(sprintf('Файл не найден или не читается: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new InvalidInputException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new InvalidInputException('Некорректный JSON: '.$e->getMessage(), 0, $e);
        }

        if (!is_array($data)) {
            throw new InvalidInputException('Корень JSON должен быть объектом');
        }

        return $this->parse($data);
    }

    /** @param array<string, mixed> $data */
    public function parse(array $data): PriceCatalogData
    {
        $providers = [];
        foreach ($this->list($data, 'providers') as $i => $p) {
            $this->requireArray($p, "providers[$i]");
            $providers[] = [
                'code' => $this->str($p, 'code', "providers[$i]"),
                'name' => $this->str($p, 'name', "providers[$i]"),
                'offPeak' => $this->parseOffPeak($p['off_peak'] ?? null, "providers[$i].off_peak"),
            ];
        }

        $models = [];
        foreach ($this->list($data, 'models') as $i => $m) {
            $this->requireArray($m, "models[$i]");
            $models[] = [
                'provider' => $this->str($m, 'provider', "models[$i]"),
                'code' => $this->str($m, 'code', "models[$i]"),
            ];
        }

        $prices = [];
        foreach ($this->list($data, 'prices') as $i => $p) {
            $this->requireArray($p, "prices[$i]");
            $where = "prices[$i]";
            $unit = PricingUnit::tryFrom($this->str($p, 'unit', $where))
                ?? throw new InvalidInputException("$where: неизвестная схема unit");

            $validFrom = $this->time($p, 'valid_from', $where);
            $rates = $this->parseRates($p['rates'] ?? null, $unit, "$where.rates");

            $prices[] = [
                'provider' => $this->str($p, 'provider', $where),
                'model' => $this->str($p, 'model', $where),
                'validFrom' => $validFrom,
                'currency' => $this->currency($p, $where),
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return new PriceCatalogData($providers, $models, $prices);
    }

    /** @return array<string, string> */
    private function parseRates(mixed $rates, PricingUnit $unit, string $where): array
    {
        $this->requireArray($rates, $where);

        $result = [];
        foreach ($unit->requiredRateKeys() as $key) {
            $value = $rates[$key] ?? throw new InvalidInputException("$where: нет ставки '$key'");
            if (!is_string($value) || preg_match(self::RATE_PATTERN, $value) !== 1) {
                throw new InvalidInputException("$where.$key: ставка должна быть десятичной строкой (до 6 знаков)");
            }
            $result[$key] = $value;
        }

        return $result;
    }

    private function parseOffPeak(mixed $value, string $where): ?OffPeakWindow
    {
        if ($value === null) {
            return null;
        }
        $this->requireArray($value, $where);

        try {
            return new OffPeakWindow(
                $this->str($value, 'window_start', $where),
                $this->str($value, 'window_end', $where),
                $this->str($value, 'multiplier', $where),
            );
        } catch (\InvalidArgumentException $e) {
            throw new InvalidInputException("$where: ".$e->getMessage(), 0, $e);
        }
    }

    /** @param array<string, mixed> $data @return list<mixed> */
    private function list(array $data, string $key): array
    {
        $value = $data[$key] ?? [];
        if (!is_array($value) || ($value !== [] && !array_is_list($value))) {
            throw new InvalidInputException("Секция '$key' должна быть списком");
        }

        return $value;
    }

    private function requireArray(mixed $value, string $where): void
    {
        if (!is_array($value)) {
            throw new InvalidInputException("$where должен быть объектом");
        }
    }

    /** @param array<array-key, mixed> $source */
    private function str(array $source, string $key, string $where): string
    {
        $value = $source[$key] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidInputException("$where.$key: ожидается непустая строка");
        }

        return $value;
    }

    /** @param array<array-key, mixed> $source */
    private function time(array $source, string $key, string $where): \DateTimeImmutable
    {
        try {
            return Time::parseIso($this->str($source, $key, $where));
        } catch (\InvalidArgumentException $e) {
            throw new InvalidInputException("$where.$key: ".$e->getMessage(), 0, $e);
        }
    }

    /** @param array<array-key, mixed> $source */
    private function currency(array $source, string $where): string
    {
        $value = $this->str($source, 'currency', $where);
        if (preg_match('/^[A-Z]{3}$/', $value) !== 1) {
            throw new InvalidInputException("$where.currency: ожидается код валюты ISO (например USD)");
        }

        return $value;
    }
}
