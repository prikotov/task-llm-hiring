<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityManagerInterface;

/** Все изменения биллинга сериализованы; отчёты читают только зафиксированные данные. */
final class WriteTransaction
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function run(callable $operation): mixed
    {
        return $this->em->wrapInTransaction(function () use ($operation): mixed {
            $this->em->getConnection()->executeQuery('SELECT pg_advisory_xact_lock(730184251)')->free();

            return $operation();
        });
    }
}
