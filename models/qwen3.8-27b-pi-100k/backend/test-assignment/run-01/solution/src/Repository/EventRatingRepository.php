<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\IsoTime;
use App\Domain\ReportRow;
use App\Entity\EventRating;
use App\Entity\PriceRow;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<EventRating> */
final class EventRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRating::class);
    }

    /** Уже рассчитанные события по строке цен, в порядке назначения. */
    public function findByPriceRow(PriceRow $priceRow): array
    {
        return $this->findBy(['priceRow' => $priceRow]);
    }

    /**
     * Строки для отчёта: рассчитанные события в период [$from, $to) по ts.
     *
     * Момента времени передаются SQL-строками с явным смещением +00:00 —
     * сравнение timestamptz не зависит от часового пояса сессии.
     *
     * @return list<ReportRow>
     */
    public function reportRows(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $sql = <<<'SQL'
                SELECT e.user_login AS user,
                       e.provider_code,
                       e.model_code,
                       e.tokens_input,
                       e.tokens_output,
                       e.tokens_cache_read,
                       e.tokens_cache_write,
                       r.cost
                FROM usage_events e
                JOIN event_ratings r ON r.event_id = e.id
            SQL;
        $params = [];
        $where = [];
        if ($from !== null) {
            $where[] = 'e.ts >= ?';
            $params[] = IsoTime::toSqlUtcString($from);
        }
        if ($to !== null) {
            $where[] = 'e.ts < ?';
            $params[] = IsoTime::toSqlUtcString($to);
        }
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.id';

        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative($sql, $params);

        $result = [];
        foreach ($rows as $row) {
            $result[] = new ReportRow(
                (string) $row['user'],
                (string) $row['provider_code'],
                (string) $row['model_code'],
                (int) $row['tokens_input'],
                (int) $row['tokens_output'],
                (int) $row['tokens_cache_read'],
                (int) $row['tokens_cache_write'],
                (string) $row['cost'],
            );
        }

        return $result;
    }
}
