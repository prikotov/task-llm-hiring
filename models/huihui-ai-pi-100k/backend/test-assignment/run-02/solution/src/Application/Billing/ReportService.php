<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Shared\Decimal;
use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт `app:billing:report` (п. 6, 7 задания):
 *  - период `--from` включительно, `--to` исключительно, по `ts` событий;
 *  - unrated-события никогда не попадают в отчёт;
 *  - строки по парам (user, model), сортировка по user, затем по model
 *    (лексикографически, возрастанию);
 *  - cost_usd — сумма уже округлённых стоимостей событий группы,
 *    ровно 6 знаков; суммы событий — без повторного округления;
 *  - CSV: UTF-8, разделитель «,», перевод строки «\n».
 */
final class ReportService
{
    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function buildCsv(null|DateTimeImmutable $from, null|DateTimeImmutable $to): string
    {
        $em = $this->em;

        $where = ['e.status = :status'];
        $params = ['status' => UsageStatus::Rated];
        if ($from !== null) {
            $where[] = 'e.ts >= :from';
            $params['from'] = [$from, Types::DATETIME_IMMUTABLE];
        }
        if ($to !== null) {
            $where[] = 'e.ts < :to';
            $params['to'] = [$to, Types::DATETIME_IMMUTABLE];
        }

        $events = $em->createQuery(
            'SELECT e FROM App\\Domain\\Usage\\UsageEvent e
             WHERE '.implode(' AND ', $where).'
             ORDER BY e.id ASC'
        );
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $events->setParameter($name, $value[0], $value[1]);
            } else {
                $events->setParameter($name, $value);
            }
        }
        $events = $events->getResult();

        /** @var array<string, array{user:string, model:string, input:int, output:int, cache_read:int, cache_write:int, cost:string}> $groups */
        $groups = [];

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $user = $event->getUser();
            $model = $event->getProvider()->getCode().'/'.$event->getModel()->getCode();
            $key = $user."\x1f".$model;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'input' => 0,
                    'output' => 0,
                    'cache_read' => 0,
                    'cache_write' => 0,
                    'cost' => '0',
                ];
            }

            $groups[$key]['input'] += $event->getTokensInput();
            $groups[$key]['output'] += $event->getTokensOutput();
            $groups[$key]['cache_read'] += $event->getTokensCacheRead();
            $groups[$key]['cache_write'] += $event->getTokensCacheWrite();
            $groups[$key]['cost'] = Decimal::add($groups[$key]['cost'], $event->getCost() ?? '0');
        }

        $lines = [self::HEADER];
        $totals = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0, 'cost' => '0'];

        uasort($groups, static function (array $a, array $b): int {
            $byUser = strcmp($a['user'], $b['user']);
            if ($byUser !== 0) {
                return $byUser;
            }

            return strcmp($a['model'], $b['model']);
        });

        foreach ($groups as $group) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $group['user'],
                $group['model'],
                $group['input'],
                $group['output'],
                $group['cache_read'],
                $group['cache_write'],
                Decimal::format6($group['cost']),
            );
            $totals['input'] += $group['input'];
            $totals['output'] += $group['output'];
            $totals['cache_read'] += $group['cache_read'];
            $totals['cache_write'] += $group['cache_write'];
            $totals['cost'] = Decimal::add($totals['cost'], $group['cost']);
        }

        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totals['input'],
            $totals['output'],
            $totals['cache_read'],
            $totals['cache_write'],
            Decimal::format6($totals['cost']),
        );

        return implode("\n", $lines)."\n";
    }
}
