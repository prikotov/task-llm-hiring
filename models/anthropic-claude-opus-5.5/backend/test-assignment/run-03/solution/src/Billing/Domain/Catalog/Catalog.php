<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\Provider;

/**
 * Порт справочника провайдеров и моделей.
 */
interface Catalog
{
    /**
     * @return list<Provider>
     */
    public function providers(): array;

    public function provider(string $code): ?Provider;

    /**
     * Идемпотентный upsert провайдера по code.
     */
    public function upsertProvider(Provider $provider): void;

    /**
     * Идемпотентный upsert модели по (provider, code). Провайдер должен
     * существовать в справочнике.
     */
    public function upsertModel(ModelReference $model): void;

    public function hasModel(ModelReference $model): bool;

    /**
     * Оф-пиковые окна всех провайдеров: код провайдера => окно.
     *
     * @return array<string, \App\Billing\Domain\Model\OffPeakWindow>
     */
    public function offPeakWindows(): array;
}
