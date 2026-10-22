<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Common\Decimal;
use App\Persistence\Repository\UsageEventRepository;
use DateTimeImmutable;

/**
 * Сборка CSV-отчёта (stdout, UTF-8, «,», «\n»).
 *
 * Группы — (user, provider/model), строки отсортированы по user, затем по
 * model лексикографически (побайтовые сравнения, без учёта локали).
 * Суммы — по уже рассчитаным событиям: токены складываются как целые,
 * cost_usd — как сумма уже округлённых стоимостей событий,
 * без повторного округления слагаемых (п. 4 TASK.md).
 * unrated-события в отчёт не попадают никогда.
 */
final readonly class ReportBuilder
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private UsageEventRepository $events)
    {
    }

    /**
     * @param DateTimeImmutable|null $from включительно
     * @param DateTimeImmutable|null $to   исключительно
     */
    public function build(?DateTimeImmutable $from, ?DateTimeImmutable $to): string
    {
        /**
         * @var array<string, array{
         *   user: string, model: string,
         *   input: int, output: int, cacheRead: int, cacheWrite: int,
         *   cost: Decimal
         * }> $groups
         */
        $groups = [];

        foreach ($this->events->findRatedInRange($from, $to) as $event) {
            if ($event->billedCost === null) {
                continue;
            }

            $model = $event->provider.'/'.$event->model;
            $key = $event->user."\0".$model;
            $group = $groups[$key] ?? [
                'user' => $event->user,
                'model' => $model,
                'input' => 0,
                'output' => 0,
                'cacheRead' => 0,
                'cacheWrite' => 0,
                'cost' => Decimal::zero(),
            ];

            $group['input'] += $event->tokensInput;
            $group['output'] += $event->tokensOutput;
            $group['cacheRead'] += $event->tokensCacheRead;
            $group['cacheWrite'] += $event->tokensCacheWrite;
            $group['cost'] = $group['cost']->add(Decimal::of($event->billedCost));

            $groups[$key] = $group;
        }

        $items = array_values($groups);
        usort($items, static fn (array $a, array $b): int =>
            strcmp($a['user'], $b['user']) ?: strcmp($a['model'], $b['model']));

        $total = ['input' => 0, 'output' => 0, 'cacheRead' => 0, 'cacheWrite' => 0, 'cost' => Decimal::zero()];

        $lines = [self::HEADER];
        foreach ($items as $group) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $group['user'],
                $group['model'],
                $group['input'],
                $group['output'],
                $group['cacheRead'],
                $group['cacheWrite'],
                $group['cost']->format(Decimal::MONEY_SCALE),
            );

            $total['input'] += $group['input'];
            $total['output'] += $group['output'];
            $total['cacheRead'] += $group['cacheRead'];
            $total['cacheWrite'] += $group['cacheWrite'];
            $total['cost'] = $total['cost']->add($group['cost']);
        }

        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $total['input'],
            $total['output'],
            $total['cacheRead'],
            $total['cacheWrite'],
            $total['cost']->format(Decimal::MONEY_SCALE),
        );

        return implode("\n", $lines)."\n";
    }
}
