<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\PriceRow;
use App\Domain\PricingScheme;

/**
 * Загрузка истории цен из JSON-файла в строки PriceRow.
 *
 * Валидирует структуру и схему тарификации; ставки и валюта
 * проверяются импортером на уровне строк.
 */
final class PriceLoader
{
    /**
     * @return array<PriceRow>
     */
    public function load(string $path): array
    {
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException("cannot read prices file: $path");
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("invalid prices JSON: " . json_last_errmsg());
        }

        $rows = [];
        foreach (($data['prices'] ?? []) as $entry) {
            $rows[] = $this->rowFromEntry($entry);
        }

        return $rows;
    }

    private function rowFromEntry(array $entry): PriceRow
    {
        foreach (['provider', 'model', 'valid_from', 'currency', 'unit', 'rates'] as $field) {
            if (!array_key_exists($field, $entry) || $entry[$field] === null) {
                throw new \RuntimeException("missing prices field: $field");
            }
        }

        $scheme = $this->schemeOf((string) $entry['unit']);
        $currency = (string) $entry['currency'];

        if ($currency !== 'USD') {
            throw new \RuntimeException("unsupported non-USD currency: $currency");
        }

        $rates = $this->ratesOf($entry['rates']);

        return new PriceRow(
            (string) $entry['provider'],
            (string) $entry['model'],
            (string) $entry['valid_from'],
            $currency,
            $scheme,
            $rates
        );
    }

    private function schemeOf(string $unit): PricingScheme
    {
        return match ($unit) {
            PricingScheme::PER_1M_TOKENS->value => PricingScheme::PER_1M_TOKENS,
            PricingScheme::PER_1M_TOKENS_UNIFIED->value => PricingScheme::PER_1M_TOKENS_UNIFIED,
            default => throw new \RuntimeException("unsupported pricing unit: $unit"),
        };
    }

    /**
     * @param mixed $rates
     * @return array<string,string>
     */
    private function ratesOf(mixed $rates): array
    {
        if (!is_array($rates)) {
            throw new \RuntimeException("prices rates must be an object");
        }
        $result = [];
        foreach ($rates as $type => $value) {
            if (is_string($type) && is_string($value)) {
                $result[$type] = $value;
            }
        }
        if ($result === []) {
            throw new \RuntimeException("prices rates are empty");
        }

        return $result;
    }
}
