<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEventRow;
use Doctrine\ORM\EntityManagerInterface;

final class UsageEventRowRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** Вставляет, только если (provider, providerRequestId) — новое (п. 5 задания). */
    public function insertIfNew(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        string $tokensInput,
        string $tokensOutput,
        string $tokensCacheRead,
        string $tokensCacheWrite,
    ): bool {
        $exists = $this->em->createQuery(
            'SELECT COUNT(e.id) FROM App\Entity\UsageEventRow e WHERE e.provider = :p AND e.providerRequestId = :rid'
        )
            ->setParameter('p', $provider)
            ->setParameter('rid', $providerRequestId)
            ->getSingleScalarResult();

        if ((int) $exists > 0) {
            return false;
        }

        $e = new UsageEventRow();
        $e->provider = $provider;
        $e->providerRequestId = $providerRequestId;
        $e->model = $model;
        $e->user = $user;
        $e->ts = $ts;
        $e->tokensInput = $tokensInput;
        $e->tokensOutput = $tokensOutput;
        $e->tokensCacheRead = $tokensCacheRead;
        $e->tokensCacheWrite = $tokensCacheWrite;
        $this->em->persist($e);

        return true;
    }

    /** @return UsageEventRow[] */
    public function findNotRated(): array
    {
        return $this->em->getRepository(UsageEventRow::class)
            ->createQueryBuilder('e')
            ->where('NOT EXISTS (SELECT 1 FROM App\\Entity\\Rating r WHERE r.eventId = e.id)')
            ->getQuery()
            ->getResult();
    }

    /** @return UsageEventRow[] */
    public function all(): array
    {
        return $this->em->getRepository(UsageEventRow::class)->findAll();
    }

    public function find(int $id): ?UsageEventRow
    {
        return $this->em->getRepository(UsageEventRow::class)->find($id);
    }
}
