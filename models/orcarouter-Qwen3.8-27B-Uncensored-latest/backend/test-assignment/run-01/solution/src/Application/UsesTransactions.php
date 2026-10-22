<?php

declare(strict_types=1);

namespace App\Application;

use Doctrine\ORM\EntityManagerInterface;

/** Запуск замыкания в одной транзакции (commit на успехе, rollback на исключении). */
trait UsesTransactions
{
    /**
     * @param callable $work
     *
     * @return mixed результат замыкания
     */
    private function inTransaction(callable $work): mixed
    {
        $em = $this->em;
        $em->beginTransaction();
        try {
            $result = $work();
            // ORM 3: commit() фиксирует DBAL-транзакцию, но НЕ прогоняет UnitOfWork —
            // flush вызывается явно.
            $em->flush();
            $em->commit();

            return $result;
        } catch (\Throwable $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollBack();
            }
            throw $e;
        }
    }
}
