<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityManagerInterface;

/** Атомарное выполнение блока работы с ORM (транзакция вокруг flush). */
final class Transactor
{
    /**
     * @template T
     *
     * @param callable(EntityManagerInterface): T $work
     *
     * @return T
     */
    public static function run(EntityManagerInterface $em, callable $work): mixed
    {
        $result = $em->getConnection()->transactional(static function () use ($em, $work) {
            $value = $work($em);
            $em->flush();

            return $value;
        });

        return $result;
    }
}
