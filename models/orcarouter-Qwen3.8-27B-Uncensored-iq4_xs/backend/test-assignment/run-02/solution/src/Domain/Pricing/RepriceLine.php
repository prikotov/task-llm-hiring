<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Common\ImportFormatException;
use App\Domain\Common\Input;

/**
 * Исправленная строка цен (входные данные app:billing:reprice).
 * Также используется как общий формат строки цен при импорте
 * справочника — валидация единая для обоих потоков.
 */
final class RepriceLine
{
    /** @param array<string, string> $rates */
    private function __construct(
        private readonly string $providerCode,
        private readonly string $modelCode,
        private readonly \DateTimeImmutable $validFrom,
        private readonly string $currency,
        private readonly PriceScheme $scheme,
        private readonly array $rates,
    ) {
    }

    /**
     * Парсит и валидирует строку цен из входного JSON.
     *
     * @param array<string, mixed> $row
     * @throws Input\ImportFormatException
     */
    public static function fromArray(array $row, string $label): self
    {
        $providerCode = Input::asNonEmptyString($row['provider'] ?? null, $label.'[provider]');
        $modelCode = Input::asNonEmptyString($row['model'] ?? null, $label.'[model]');

        $validFrom = Input::parseIsoUtc((string) ($row['valid_from'] ?? ''));
        if ($validFrom === null) {
            throw new ImportFormatException($label.'[valid_from]: ожидается ISO-8601 (UTC, "Z")');
        }

        $unit = PriceScheme::tryFrom((string) ($row['unit'] ?? ''));
        if ($unit === null) {
            throw new ImportFormatException($label.'[unit]: ожидается "per_1m_tokens" или "per_1m_tokens_unified"');
        }

        $rawRates = $row['rates'] ?? null;
        if (!\is_array($rawRates)) {
            throw new ImportFormatException($label.'[rates]: ожидается объект');
        }

        $rates = self::validateRates($rawRates, $unit, $label);
        $currency = Input::asNonEmptyString($row['currency'] ?? null, $label.'[currency]');

        return new self($providerCode, $modelCode, $validFrom, $currency, $unit, $rates);
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, string> ровно набор ставок схемы, десятичными строками
     */
    private static function validateRates(array $raw, PriceScheme $unit, string $label): array
    {
        $keys = $unit->rateKeys();
        $result = [];
        foreach ($keys as $key) {
            $value = $raw[$key] ?? null;
            if (!\is_string($value) || !Input::isDecimalString($value)) {
                throw new ImportFormatException($label.'[rates.'.$key.']: ожидается десятичная строка (не число JSON)');
            }
            $result[$key] = $value;
        }

        $unknown = array_diff(array_keys($raw), $keys);
        if ($unknown !== []) {
            throw new ImportFormatException(
                $label.'[rates]: неизвестные ключи — '.implode(', ', array_map('strval', $unknown)),
            );
        }

        return $result;
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function modelCode(): string
    {
        return $this->modelCode;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function scheme(): PriceScheme
    {
        return $this->scheme;
    }

    /** @return array<string, string> */
    public function rates(): array
    {
        return $this->rates;
    }
}
