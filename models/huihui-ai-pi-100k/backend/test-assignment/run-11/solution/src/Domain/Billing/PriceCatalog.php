<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Порт: справочник цен (провайдеры и строки цен) для тарификации.
 */
interface PriceCatalog
{
    public function findProvider(string $code): ?ProviderInfo;

    /**
     * Строка цен модели, действовавшая на момент $ts (max valid_from ≤ ts),
     * либо null — событие остаётся unrated.
     */
    public function findPriceRowFor(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $ts,
    ): ?\App\Domain\Pricing\StoredPriceRow;

    /**
     * Все строки цен модели (для reprice-пересчёта).
     *
     * @return array<int, \App\Domain\Pricing\StoredPriceRow>
     */
    public function allPriceRowsFor(string $providerCode, string $modelCode): array;

    /**
     * Все модели провайдера (для проверки существования при reprice).
     *
     * @return array<int, string>
     */
    public function providerModels(string $providerCode): array;
}
