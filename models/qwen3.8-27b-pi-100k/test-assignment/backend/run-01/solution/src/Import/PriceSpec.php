<?php

declare(strict_types=1);

namespace App\Import;

/** Строка цены из файла истории цен (проверенная парсером). */
final class PriceSpec
{
    /** @param array<string,string> $rates ключи — по схеме unit */
    public function __construct(
        private readonly string $providerCode,
        private readonly string $model,
        private readonly \DateTimeImmutable $validFrom,
        private readonly string $currency,
        private readonly string $unit,
        private readonly array $rates,
    ) {
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    /** @return array<string,string> */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     Сравнение сущностного содержимого со строкой, сохранённой в БД
     (идентичность unit + currency + нормализованных rates).
     *
     * @param array{unit:string, currency:string, rates:array<string,string>} $stored
     */
    public function sameContent(array $stored): bool
    {
        return $this->unit === $stored['unit']
            && $this->currency === $stored['currency']
            && $this->rates === $stored['rates'];
    }
}
