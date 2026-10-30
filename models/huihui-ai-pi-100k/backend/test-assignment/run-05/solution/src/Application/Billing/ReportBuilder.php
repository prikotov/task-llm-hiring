<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money\MoneyMath;
use App\Infrastructure\Persistence\Entity\UsageEvent;
use App\Infrastructure\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Построение CSV-отчёта (команда app:billing:report).
 *
 *  - строки — по парам (user, model), отсортированы по user,
 *    затем по model (лексикографически);
 *  - cost_usd — сумма уже округлённых стоимостей событий, без
 *    повторного округления слагаемых (bcmath, масштаб 6);
 *  - unrated-события не попадают;
 *  - период: [from, to);
 *  - TOTAL — последняя строка, пустое поле model.
 */
final class ReportBuilder
{
    public const CSV_HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return string готовый CSV (кодировка UTF-8, EOL=\n, завершён "\n")
     */
    public function build(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        /** @var UsageEvent[] $events */
        $events = $this->em
            ->getRepository(UsageEvent::class)
            ->findRatedBetween($from, $to);

        /** @var array<int, array{user:string, model:string, ti:int, to:int, cr:int, cw:int, cost:string}> $groups */
        $groups = [];
        $totals = ['ti' => 0, 'to' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];

        foreach ($events as $event) {
            $modelRef = $event->provider . '/' . $event->model;
            $key = $event->user . '\u{1F}' . $modelRef;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->user, 'model' => $modelRef,
                    'ti' => 0, 'to' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0',
                ];
            }

            $g = &$groups[$key];
            $g['ti'] += $event->tokensInput;
            $g['to'] += $event->tokensOutput;
            $g['cr'] += $event->tokensCacheRead;
            $g['cw'] += $event->tokensCacheWrite;
            $g['cost'] = bcadd($g['cost'], (string) $event->cost, MoneyMath::AMOUNT_SCALE);
            unset($g);

            $totals['ti'] += $event->tokensInput;
            $totals['to'] += $event->tokensOutput;
            $totals['cr'] += $event->tokensCacheRead;
            $totals['cw'] += $event->tokensCacheWrite;
            $totals['cost'] = bcadd($totals['cost'], (string) $event->cost, MoneyMath::AMOUNT_SCALE);
        }

        $lines = [self::CSV_HEADER];

        $rows = array_values($groups);
        usort($rows, static function (array $a, array $b): int {
            return $a['user'] <=> $b['user'] ?: $a['model'] <=> $b['model'];
        });

        foreach ($rows as $row) {
            $lines[] = implode(',', [
                self::csvField($row['user']),
                self::csvField($row['model']),
                (string) $row['ti'],
                (string) $row['to'],
                (string) $row['cr'],
                (string) $row['cw'],
                self::fmt($row['cost']),
            ]);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $totals['ti'],
            (string) $totals['to'],
            (string) $totals['cr'],
            (string) $totals['cw'],
            self::fmt($totals['cost']),
        ]);

        return implode("\n", $lines) . "\n";
    }

    /**
     * Формат стоимости: ровно AMOUNT_SCALE знаков после точки
     * (например "0" → "0.000000").
     */
    private static function fmt(string $cost): string
    {
        return bcadd('0', $cost, MoneyMath::AMOUNT_SCALE);
    }

    /**
     * Экранирование CSV-поля (RFC 4180): кавычки, если есть
     * запятая/кавычка/перенос строки; кавычки внутри — удвоаются.
     */
    private static function csvField(string $value): string
    {
        if (preg_match('/[",\r\n]/', $value) === 1) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }
}
