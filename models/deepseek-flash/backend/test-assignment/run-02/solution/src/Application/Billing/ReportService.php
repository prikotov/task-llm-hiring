<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Money\Money;
use App\Domain\Usage\Entity\UsageEvent;
use App\Domain\Usage\ValueObject\RateStatus;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ReportService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function generate(?DateTimeImmutable $from, ?DateTimeImmutable $to): string
    {
        $qb = $this->em->createQueryBuilder()
            ->select(
                'e.userId AS user_id',
                'e.providerCode AS provider_code',
                'e.modelCode AS model_code',
                'SUM(e.tokensInput) AS tokens_input',
                'SUM(e.tokensOutput) AS tokens_output',
                'SUM(e.tokensCacheRead) AS tokens_cache_read',
                'SUM(e.tokensCacheWrite) AS tokens_cache_write',
                'SUM(e.costUsd) AS cost_usd',
            )
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', RateStatus::Rated)
            ->groupBy('e.userId')
            ->addGroupBy('e.providerCode')
            ->addGroupBy('e.modelCode')
            ->orderBy('e.userId', 'ASC')
            ->addOrderBy('e.providerCode', 'ASC')
            ->addOrderBy('e.modelCode', 'ASC');

        if ($from !== null) {
            $qb->andWhere('e.occurredAt >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.occurredAt < :to')->setParameter('to', $to);
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = '0.000000';

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];

        foreach ($rows as $row) {
            $input = (int) $row['tokens_input'];
            $output = (int) $row['tokens_output'];
            $cacheRead = (int) $row['tokens_cache_read'];
            $cacheWrite = (int) $row['tokens_cache_write'];
            $cost = Money::format((string) $row['cost_usd']);

            $totalInput += $input;
            $totalOutput += $output;
            $totalCacheRead += $cacheRead;
            $totalCacheWrite += $cacheWrite;
            $totalCost = Money::add($totalCost, $cost, Money::MONEY_SCALE);

            $lines[] = implode(',', [
                self::csv((string) $row['user_id']),
                self::csv($row['provider_code'].'/'.$row['model_code']),
                (string) $input,
                (string) $output,
                (string) $cacheRead,
                (string) $cacheWrite,
                $cost,
            ]);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $totalInput,
            (string) $totalOutput,
            (string) $totalCacheRead,
            (string) $totalCacheWrite,
            Money::format($totalCost),
        ]);

        return implode("\n", $lines)."\n";
    }

    private static function csv(string $value): string
    {
        if (strpbrk($value, ",\"\n\r") === false) {
            return $value;
        }

        return '"'.str_replace('"', '""', $value).'"';
    }
}
