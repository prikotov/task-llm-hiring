<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Catalog\PriceLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<UsageEvent> */
final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function findByKey(string $provider, string $providerRequestId): ?UsageEvent
    {
        return $this->createQueryBuilder('e')
            ->where('e.provider = :provider')
            ->andWhere('e.providerRequestId = :id')
            ->setParameter('provider', $provider)
            ->setParameter('id', $providerRequestId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** События ещё не рассчитанные (включая предыдущие unrated). */
    public function findPending(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_PENDING)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Рассчитанные события, тарифицировавшиеся указанной строкой цен. */
    public function findRatedByPriceLine(PriceLine $line): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.priceLine = :line')
            ->andWhere('e.status = :status')
            ->setParameter('line', $line)
            ->setParameter('status', UsageEvent::STATUS_RATED)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события в периоде [from, to) — для отчёта.
     * @return array<int, array{user:string, model_label:string,
     *     tokens_input:int, tokens_output:int, tokens_cache_read:int,
     *     tokens_cache_write:int, cost:string}>
     */
    public function findRatedInRange(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->createQueryBuilder();
        $qb->select('ue.user_code AS user', 'ue.provider AS model_provider', 'ue.model AS model_code')
            ->addSelect('ue.tokens_input', 'ue.tokens_output', 'ue.tokens_cache_read', 'ue.tokens_cache_write', 'ue.cost')
            ->from('usage_event', 'ue')
            ->where('ue.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('ue.ts >= :from')->setParameter('from', $this->sqlDateTime($from));
        }
        if ($to !== null) {
            $qb->andWhere('ue.ts < :to')->setParameter('to', $this->sqlDateTime($to));
        }

        $rows = $qb->fetchAllAssociative();

        return array_map(static fn (array $row): array => [
            'user' => (string) $row['user'],
            'model_label' => $row['model_provider'].'/'.$row['model_code'],
            'tokens_input' => (int) $row['tokens_input'],
            'tokens_output' => (int) $row['tokens_output'],
            'tokens_cache_read' => (int) $row['tokens_cache_read'],
            'tokens_cache_write' => (int) $row['tokens_cache_write'],
            'cost' => (string) $row['cost'],
        ], $rows);
    }

    /** Колонки ts — тип datetime без дробной части. */
    private function sqlDateTime(\DateTimeImmutable $ts): string
    {
        return $ts->format('Y-m-d H:i:s');
    }
}
