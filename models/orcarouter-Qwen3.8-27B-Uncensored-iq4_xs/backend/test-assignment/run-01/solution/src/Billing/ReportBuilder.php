<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\UsageEvent;
use App\Util\MoneyCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам: CSV в stdout.
 *
 * Строки — по паре (user, model), где model = "<provider>/<code>";
 * количество токенов и стоимость — суммы по событиям группы.
 * Стоимость — сумма уже округлённых стоимостей событий, без повторного
 * округления слагаемых. Период: from включительно, to исключительно.
 * Unrated-события в отчёт не попадают никогда (у них нет стоимости).
 */
final class ReportBuilder
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';
    public const TOTAL_USER = 'TOTAL';

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param \DateTimeImmutable|null $from включительно
     * @param \DateTimeImmutable|null $to исключительно
     * @return string готовый CSV (завершается символом новой строки)
     */
    public function build(\DateTimeImmutable|null $from, \DateTimeImmutable|null $to): string
    {
        $query = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $query->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $query->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        /**
         * Группы "user\0model": модель — "<provider>/<code>";
         * стоимость хранится как десятичная строка (bcmath).
         *
         * @var array<string, array{model: string, ti: int, to: int, cr: int, cw: int, cost: string}> $groups
         */
        $groups = [];
        $totals = ['ti' => 0, 'to' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];

        /** @var UsageEvent $event */
        foreach ($query->getQuery()->toIterable() as $event) {
            $key = $event->getUser()."\x00".$this->modelLabel($event);

            $group = $groups[$key] ?? [
                'model' => $this->modelLabel($event),
                'ti' => 0,
                'to' => 0,
                'cr' => 0,
                'cw' => 0,
                'cost' => '0',
            ];

            $tokens = $event->tokens();
            $group['ti'] += $tokens['input'];
            $group['to'] += $tokens['output'];
            $group['cr'] += $tokens['cache_read'];
            $group['cw'] += $tokens['cache_write'];
            $group['cost'] = MoneyCalculator::add($group['cost'], (string) $event->getCostUsd());
            $groups[$key] = $group;

            $totals['ti'] += $tokens['input'];
            $totals['to'] += $tokens['output'];
            $totals['cr'] += $tokens['cache_read'];
            $totals['cw'] += $tokens['cache_write'];
            $totals['cost'] = MoneyCalculator::add($totals['cost'], (string) $event->getCostUsd());
        }

        // "\x00" меньше любого печатного символа: по возрастанию строки
        // "user\0model" — это лексикографическая сортировка (user, model).
        ksort($groups);

        $lines = [self::HEADER];
        foreach ($groups as $key => $group) {
            $user = \strstr($key, "\x00", true);
            $lines[] = implode(',', [
                $user,
                $group['model'],
                (string) $group['ti'],
                (string) $group['to'],
                (string) $group['cr'],
                (string) $group['cw'],
                MoneyCalculator::normalize($group['cost']),
            ]);
        }

        $lines[] = implode(',', [
            self::TOTAL_USER,
            '',
            (string) $totals['ti'],
            (string) $totals['to'],
            (string) $totals['cr'],
            (string) $totals['cw'],
            MoneyCalculator::normalize($totals['cost']),
        ]);

        return implode("\n", $lines)."\n";
    }

    private function modelLabel(UsageEvent $event): string
    {
        return $event->getProviderCode().'/'.ltrim($event->getModelCode(), '/');
    }
}
