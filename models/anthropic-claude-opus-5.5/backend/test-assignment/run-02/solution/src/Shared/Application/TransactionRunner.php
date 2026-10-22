<?php

declare(strict_types=1);

namespace App\Shared\Application;

interface TransactionRunner
{
    /**
     * Выполняет $operation в одной транзакции. Если задан $exclusiveLock —
     * дополнительно берёт эксклюзивную блокировку с таким именем до конца
     * транзакции (сериализация конкурентных запусков тарификации).
     *
     * @template T
     *
     * @param callable(): T $operation
     *
     * @return T
     */
    public function run(callable $operation, ?string $exclusiveLock = null): mixed;
}
