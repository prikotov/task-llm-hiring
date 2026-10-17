<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\Entity\UsageEvent;
use App\Domain\Billing\Exception\BillingException;
use App\Domain\Billing\Util\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

/**
 * Отчёт расходов: CSV (UTF-8, `,`, "\n") по агрегированным парам (user, model).
 *
 * В отчёт попадают только рассчитанные (rated) события:
 * cost — сумма уже округлённых стоимостей событий группы, без повторного
 * округления слагаемых (Money::add работает строками). TOTAL-строка —
 * сумма по всем группам отчёта.
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return list<list<string|int>> строки CSV (со строкой заголовка и TOTAL)
     */
    public function report(?string $from, ?string $to): array
    {
        [$fromTs, $toTs] = $this->parsePeriod($from, $to);

        $qb = $this->em->getRepository(UsageEvent::class)->createQueryBuilder('e')
            ->where('e.rated = true');

        $params = [];
        if ($fromTs !== null) {
            $qb->andWhere('e.ts >= :from');
            $params['from'] = $fromTs;
        }
        if ($toTs !== null) {
            $qb->andWhere('e.ts < :to');
            $params['to'] = $toTs;
        }

        // ORM 3: QueryBuilder::setParameters() принимает ArrayCollection<Parameter>.
        $parameters = new ArrayCollection();
        foreach ($params as $key => $value) {
            $parameters->add(new Parameter($key, $value));
        }

        $events = $qb->orderBy('e.ts', 'ASC')->setParameters($parameters)->getQuery()->getResult();

        /** @var array<string, array{user: string, model: string, in: int, out: int, cr: int, cw: int, cost: string}> $groups */
        $groups = [];
        foreach ($events as $event) {
            $key = $event->user.'|'.$event->provider.'/'.$event->model;
            $g = &$groups[$key];
            if (!isset($g)) {
                $g = [
                    'user' => $event->user,
                    'model' => $event->provider.'/'.$event->model,
                    'in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0,
                    'cost' => '0',
                ];
            }
            $g['in'] += $event->tokensInput;
            $g['out'] += $event->tokensOutput;
            $g['cr'] += $event->tokensCacheRead;
            $g['cw'] += $event->tokensCacheWrite;
            $g['cost'] = Money::add($g['cost'], (string) $event->cost);
            unset($g);
        }

        ksort($groups);

        $rows = [['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd']];
        $totals = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];

        foreach ($groups as $g) {
            $rows[] = [
                $g['user'],
                $g['model'],
                (string) $g['in'],
                (string) $g['out'],
                (string) $g['cr'],
                (string) $g['cw'],
                Money::roundHalfUp($g['cost']),
            ];
            $totals['in'] += $g['in'];
            $totals['out'] += $g['out'];
            $totals['cr'] += $g['cr'];
            $totals['cw'] += $g['cw'];
            $totals['cost'] = Money::add($totals['cost'], $g['cost']);
        }

        $rows[] = [
            'TOTAL',
            '',
            (string) $totals['in'],
            (string) $totals['out'],
            (string) $totals['cr'],
            (string) $totals['cw'],
            Money::roundHalfUp($totals['cost']),
        ];

        return $rows;
    }

    /** @return array{0: ?\DateTimeImmutable, 1: ?\DateTimeImmutable} */
    private function parsePeriod(?string $from, ?string $to): array
    {
        return [
            $from === null ? null : $this->parseIso($from, '--from'),
            $to === null ? null : $this->parseIso($to, '--to'),
        ];
    }

    private function parseIso(string $value, string $option): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value, new \DateTimeZone("UTC"));
        } catch (\Exception $e) {
            throw new BillingException(sprintf('%s: некорректная дата ISO-8601 "%s".', $option, $value));
        }
    }
}
