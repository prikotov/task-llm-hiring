<?php

declare(strict_types=1);

namespace App\Import;

/**
 * Разобранный и полностью проверенный файл истории цен.
 * Внутренние списки — без дублей по естественным ключам (дубли в одном
 * файле разрешаются правилом «последний записанный побеждает»).
 */
final class PriceFile
{
    /**
     * @param ProviderSpec[] $providers ключ — code
     * @param ModelSpec[]    $models    ключ — provider|code
     * @param PriceSpec[]    $prices    ключ — provider|model|unix(validFrom)
     */
    public function __construct(
        private readonly array $providers,
        private readonly array $models,
        private readonly array $prices,
    ) {
    }

    /** @return ProviderSpec[] */
    public function getProviders(): array
    {
        return array_values($this->providers);
    }

    /** @return ModelSpec[] */
    public function getModels(): array
    {
        return array_values($this->models);
    }

    /** @return PriceSpec[] */
    public function getPrices(): array
    {
        return array_values($this->prices);
    }

    public function findProvider(string $code): ?ProviderSpec
    {
        return $this->providers[$code] ?? null;
    }

    public function findModel(string $providerCode, string $code): ?ModelSpec
    {
        return $this->models[$providerCode.'|'.$code] ?? null;
    }
}
