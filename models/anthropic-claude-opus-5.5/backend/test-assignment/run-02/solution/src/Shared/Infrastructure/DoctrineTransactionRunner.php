<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\TransactionRunner;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTransactionRunner implements TransactionRunner
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function run(callable $operation, ?string $exclusiveLock = null): mixed
    {
        return $this->em->wrapInTransaction(function () use ($operation, $exclusiveLock): mixed {
            if ($exclusiveLock !== null) {
                $this->em->getConnection()->executeStatement(
                    'SELECT pg_advisory_xact_lock(hashtext(:name))',
                    ['name' => $exclusiveLock],
                );
            }

            return $operation();
        });
    }
}
