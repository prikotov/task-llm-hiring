<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Billing;
use App\Billing\Entity\UsageEvent;
use App\Billing\Repository\UsageEventRepository;
use App\Billing\ValueObject\Decimal;
use DateTimeImmutable;

/**
 * Отчёт в CSV (stdout-контракт app:billing:report).
 *
 * Строки — по парам (user, model), сортировка по user, затем по model;
 * cost — сумма уже округлённых стоимостей событий (повторного округления нет);
 * unrated-события в отчёт не попадают.
 */
final class ReportService
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private readonly UsageEventRepository $events)
    {
    }

    public function build(?DateTimeImmutable $from, ?DateTimeImmutable $to): string
    {
        /** @var list<UsageEvent> $events */
        $events = $this->events->findRatedInRange($from, $to);

        /** @var array<string, array{user: string, model: string, ti: Decimal, to: Decimal, tr: Decimal, tw: Decimal, cost: Decimal}> $groups */
        $groups = [];
        $total = ['ti' => Decimal::zero(), 'to' => Decimal::zero(), 'tr' => Decimal::zero(), 'tw' => Decimal::zero(), 'cost' => Decimal::zero()];

        foreach ($events as $e) {
            $modelDisplay = $e->provider.'/'.$e->model;
            $key = $e->user."\x1f".$modelDisplay;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $e->user,
                    'model' => $modelDisplay,
                    'ti' => Decimal::zero(),
                    'to' => Decimal::zero(),
                    'tr' => Decimal::zero(),
                    'tw' => Decimal::zero(),
                    'cost' => Decimal::zero(),
                ];
            }
            // Без operator «&»: активная ссылка переменного на элемент массива +
            // последующий foreach по этому массиву — классическая ловушка PHP.
            $groups[$key]['ti'] = $groups[$key]['ti']->add(Decimal::of($e->tokensInput));
            $groups[$key]['to'] = $groups[$key]['to']->add(Decimal::of($e->tokensOutput));
            $groups[$key]['tr'] = $groups[$key]['tr']->add(Decimal::of($e->tokensCacheRead));
            $groups[$key]['tw'] = $groups[$key]['tw']->add(Decimal::of($e->tokensCacheWrite));
            $groups[$key]['cost'] = $groups[$key]['cost']->add(Decimal::of($e->cost ?? '0'));

            $total['ti'] = $total['ti']->add(Decimal::of($e->tokensInput));
            $total['to'] = $total['to']->add(Decimal::of($e->tokensOutput));
            $total['tr'] = $total['tr']->add(Decimal::of($e->tokensCacheRead));
            $total['tw'] = $total['tw']->add(Decimal::of($e->tokensCacheWrite));
            $total['cost'] = $total['cost']->add(Decimal::of($e->cost ?? '0'));
        }

        ksort($groups);

        $lines = [self::HEADER];
        foreach ($groups as $group) {
            $lines[] = implode(',', [
                $group['user'],
                $group['model'],
                $group['ti']->toFixed(0),
                $group['to']->toFixed(0),
                $group['tr']->toFixed(0),
                $group['tw']->toFixed(0),
                $group['cost']->toFixed(Billing::COST_DECIMALS),
            ]);
        }
        $lines[] = implode(',', [
            'TOTAL',
            '',
            $total['ti']->toFixed(0),
            $total['to']->toFixed(0),
            $total['tr']->toFixed(0),
            $total['tw']->toFixed(0),
            $total['cost']->toFixed(Billing::COST_DECIMALS),
        ]);

        return implode("\n", $lines)."\n";
    }
}
