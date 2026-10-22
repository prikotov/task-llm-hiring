<?php

declare(strict_types=1);

namespace App\Billing\Domain\Pricing;

use App\Billing\Domain\Model\ModelReference;

/**
 * Порт доступа к справочнику цен (в терминах DDD — репозиторий).
 * Домен не знает ни про Doctrine, ни про SQL.
 */
interface PriceCatalog
{
    /**
     * Строки цен модели, по возрастанию valid_from.
     *
     * @return list<PriceRow>
     */
    public function rowsForModel(ModelReference $model): array;

    public function rowById(int $id): ?PriceRow;

    public function rowByNaturalKey(ModelReference $model, \DateTimeImmutable $validFrom): ?PriceRow;

    /**
     * Идемпотентный upsert по (provider, model, valid_from): существующая
     * строка обновляется (id сохраняется), отсутствующая создаётся.
     *
     * @return int id итоговой строки
     */
    public function upsert(PriceRow $row): int;

    /**
     * @return list<PriceRow>
     */
    public function all(): array;
}
