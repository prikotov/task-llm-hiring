<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\PricingScheme;
use App\Entity\Price;

/**
 * Строка истории цен из JSON-файла: провайдер + модель + период действия
 * + схема тарификации + ставки.
 */
final class PriceRow
{
    /**
     * @param PricingScheme $scheme схема тарификации строки
     * @param array<string,string> $rates ставки вида => десятичная строка
     */
    public function __construct(
        private readonly string $provider,
        private readonly string $model,
        private readonly string $validFrom,
        private readonly string $currency,
        private readonly PricingScheme $scheme,
        private readonly array $rates
    ) {
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->validFrom);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getScheme(): PricingScheme
    {
        return $this->scheme;
    }

    public function getRates(): array
    {
        return $this->rates;
    }

    public function getKey(): string
    {
        return $this->provider . '/' . $this->model . '@' . $this->validFrom;
    }
}
