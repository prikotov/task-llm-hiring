<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Usage\UsageEvent;
use App\Infrastructure\Doctrine\Entity\UsageEventEntity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class UsageEventRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Существующие ключи (provider, provider_request_id) среди переданных.
     * Запрос идёт пачками, чтобы не упереться в лимит параметров.
     *
     * @param list<array{0: string, 1: string}> $providerAndRequestIds
     *
     * @return array<string, true> ключ — "provider\0requestId"
     */
    public function existingKeys(array $providerAndRequestIds): array
    {
        $existing = [];
        $unique = [];
        foreach ($providerAndRequestIds as [$provider, $requestId]) {
            $unique[$provider . "\0" . $requestId] = [$provider, $requestId];
        }

        foreach (array_chunk($unique, 500, true) as $chunk) {
            $qb = $this->em->createQueryBuilder()
                ->select('e.providerCode', 'e.providerRequestId')
                ->from(UsageEventEntity::class, 'e');

            $conditions = [];
            foreach (array_values($chunk) as $i => [$provider, $requestId]) {
                $conditions[] = sprintf('(e.providerCode = :p%d AND e.providerRequestId = :r%d)', $i, $i);
                $qb->setParameter('p' . $i, $provider);
                $qb->setParameter('r' . $i, $requestId);
            }
            $qb->andWhere(implode(' OR ', $conditions));

            /** @var list<array{providerCode: string, providerRequestId: string}> $rows */
            $rows = $qb->getQuery()->getArrayResult();
            foreach ($rows as $row) {
                $existing[$row['providerCode'] . "\0" . $row['providerRequestId']] = true;
            }
        }

        return $existing;
    }

    public function persist(UsageEvent $event): UsageEventEntity
    {
        $entity = new UsageEventEntity($event);
        $this->em->persist($entity);

        return $entity;
    }

    /**
     * События, ожидающие расчёта (cost_usd IS NULL), в стабильном порядке.
     *
     * Пагинация по id, а не по «уже прочитанным»: unrated-события остаются
     * с cost_usd IS NULL и иначе возвращались бы в каждом батче бесконечно.
     *
     * @return list<UsageEventEntity>
     */
    public function findUnrated(int $limit, int $afterId = 0): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEventEntity::class, 'e')
            ->andWhere('e.costUsd IS NULL')
            ->andWhere('e.id > :afterId')
            ->setParameter('afterId', $afterId)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события конкретной модели начиная с момента $since.
     * Идентификация модели — по (provider_code, model_code) события:
     * так обрабатываются и события, чья строка цен была удалена/заменена.
     *
     * @return list<UsageEventEntity>
     */
    public function findRatedForModel(string $providerCode, string $modelCode, DateTimeImmutable $since): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEventEntity::class, 'e')
            ->andWhere('e.costUsd IS NOT NULL')
            ->andWhere('e.providerCode = :provider')
            ->andWhere('e.modelCode = :model')
            ->andWhere('e.occurredAt >= :since')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('since', $since)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Строки отчёта: суммы токенов и стоимостей по (user, provider/model).
     *
     * @return list<array{userRef: string, providerCode: string, modelCode: string, tokensInput: int|string, tokensOutput: int|string, tokensCacheRead: int|string, tokensCacheWrite: int|string, costUsd: string}>
     */
    public function aggregate(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select(
                'e.userRef AS userRef',
                'e.providerCode AS providerCode',
                'e.modelCode AS modelCode',
                'SUM(e.tokensInput) AS tokensInput',
                'SUM(e.tokensOutput) AS tokensOutput',
                'SUM(e.tokensCacheRead) AS tokensCacheRead',
                'SUM(e.tokensCacheWrite) AS tokensCacheWrite',
                'SUM(e.costUsd) AS costUsd',
            )
            ->from(UsageEventEntity::class, 'e')
            ->andWhere('e.costUsd IS NOT NULL')
            ->groupBy('e.userRef', 'e.providerCode', 'e.modelCode');

        if (null !== $from) {
            $qb->andWhere('e.occurredAt >= :from')->setParameter('from', $from);
        }
        if (null !== $to) {
            $qb->andWhere('e.occurredAt < :to')->setParameter('to', $to);
        }

        /** @var list<array{userRef: string, providerCode: string, modelCode: string, tokensInput: int|string, tokensOutput: int|string, tokensCacheRead: int|string, tokensCacheWrite: int|string, costUsd: string}> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }
}
