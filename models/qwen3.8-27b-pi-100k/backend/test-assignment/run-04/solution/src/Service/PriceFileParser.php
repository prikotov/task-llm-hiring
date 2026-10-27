<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\OffPeak;
use App\Domain\PriceSpec;
use App\Exception\ImportException;

/**
 * Разбор и валидация файлов истории цен (JSON).
 *
 * Файл: { providers: [...], models: [...], prices: [...] }.
 * Для rетро-коррекции (app:billing:reprice) секции providers/models
 * могут отсутствовать или быть пустыми — цены валидируются по базе.
 */
final class PriceFileParser
{
    /**
     * @return array{providers: list<array<string,mixed>>, models: list<array<string,mixed>>, prices: list<array<string,mixed>>}
     */
    public function parseFile(string $file): array
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new ImportException(sprintf('файл не найден или не читается: %s', $file));
        }

        $raw = \file_get_contents($file);
        if ($raw === false) {
            throw new ImportException(sprintf('не удалось прочитать файл: %s', $file));
        }

        $data = \json_decode($raw, true);
        if (!\is_array($data)) {
            throw new ImportException(sprintf('некорректный JSON в файле %s', $file));
        }

        return [
            'providers' => self::section($data, 'providers'),
            'models' => self::section($data, 'models'),
            'prices' => self::section($data, 'prices'),
        ];
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, spec: PriceSpec}
     */
    public function parsePrice(array $raw, string $context): array
    {
        return [
            'provider' => self::nonEmptyString($raw['provider'] ?? null, 'provider', $context),
            'model' => self::nonEmptyString($raw['model'] ?? null, 'model', $context),
            'valid_from' => self::parseValidFrom($raw['valid_from'] ?? null, $context),
            'currency' => self::nonEmptyString($raw['currency'] ?? null, 'currency', $context),
            'spec' => PriceSpec::create(
                self::nonEmptyString($raw['unit'] ?? null, 'unit', $context),
                $raw['rates'] ?? [],
                $context . ' rates',
            ),
        ];
    }

    /**
     * Оф-пик провайдера: null (нет скидки) либо валидное окно.
     *
     * @param array<string, mixed> $raw
     */
    public function parseOffPeak(array $raw, string $context): ?OffPeak
    {
        if (!\array_key_exists('off_peak', $raw) || $raw['off_peak'] === null) {
            return null;
        }

        $op = $raw['off_peak'];
        if (!\is_array($op)) {
            throw new ImportException(sprintf('%s: off_peak должен быть объектом или null', $context));
        }

        return OffPeak::create(
            self::nonEmptyString($op['window_start'] ?? null, 'off_peak.window_start', $context),
            self::nonEmptyString($op['window_end'] ?? null, 'off_peak.window_end', $context),
            self::nonEmptyString($op['multiplier'] ?? null, 'off_peak.multiplier', $context),
        );
    }

    /** @param mixed $value */
    private static function parseValidFrom($value, string $context): \DateTimeImmutable
    {
        $value = self::nonEmptyString($value, 'valid_from', $context);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,9})?Z$/', $value)) {
            throw new ImportException(sprintf(
                '%s valid_from: ожидается ISO-8601 с UTC (например 2026-08-01T00:00:00Z), получено "%s"',
                $context,
                $value,
            ));
        }

        try {
            $ts = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException(sprintf('%s valid_from: неприводимая дата "%s"', $context, $value));
        }

        if ($ts->getOffset() !== 0) {
            throw new ImportException(sprintf('%s valid_from: ожидается UTC (Z), получено "%s"', $context, $value));
        }

        return $ts;
    }

    /** @param mixed $value */
    private static function nonEmptyString($value, string $field, string $context): string
    {
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf(
                '%s: поле "%s" обязательно и должно быть непустой строкой',
                $context,
                $field,
            ));
        }

        return $value;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @return list<array<string, mixed>>
     */
    private static function section(array $data, string $name): array
    {
        if (!\array_key_exists($name, $data) || $data[$name] === null) {
            return [];
        }

        $value = $data[$name];
        if (!\is_array($value) || !array_is_list($value)) {
            throw new ImportException(sprintf('секция "%s" должна быть массивом записей', $name));
        }

        return $value;
    }
}
