<?php

declare(strict_types=1);

namespace App\Application\Report;

use App\Domain\Money\Decimal;
use App\Infrastructure\Doctrine\Repository\UsageEventRepository;
use App\Infrastructure\Json\UsageEventReader;
use DateTimeImmutable;

/**
 * Отчёт по (user, model) за период [from, to).
 *
 * Суммы — сумма уже округлённых стоимостей событий (в БД они хранятся
 * с 6 знаками), повторного округления слагаемых нет: сложение идёт
 * в Decimal с большим масштабом, а форматирование — один раз при выводе.
 */
final class BuildReport
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private readonly UsageEventRepository $events)
    {
    }

    public static function parseBound(?string $value, string $option): ?DateTimeImmutable
    {
        if (null === $value) {
            return null;
        }

        try {
            return UsageEventReader::parseTimestamp($value);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('%s: %s', $option, $e->getMessage()), 0, $e);
        }
    }

    /**
     * @return array{rows: list<array{user: string, model: string, tokensInput: int, tokensOutput: int, tokensCacheRead: int, tokensCacheWrite: int, cost: Decimal}>, totals: array{tokensInput: int, tokensOutput: int, tokensCacheRead: int, tokensCacheWrite: int, cost: Decimal}}
     */
    public function __invoke(?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array
    {
        $grouped = [];

        foreach ($this->events->aggregate($from, $to) as $row) {
            $modelLabel = $row['providerCode'] . '/' . $row['modelCode'];
            $key = $row['userRef'] . "\0" . $modelLabel;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'user' => $row['userRef'],
                    'model' => $modelLabel,
                    'tokensInput' => 0,
                    'tokensOutput' => 0,
                    'tokensCacheRead' => 0,
                    'tokensCacheWrite' => 0,
                    'cost' => Decimal::zero(),
                ];
            }

            $grouped[$key]['tokensInput'] += (int) $row['tokensInput'];
            $grouped[$key]['tokensOutput'] += (int) $row['tokensOutput'];
            $grouped[$key]['tokensCacheRead'] += (int) $row['tokensCacheRead'];
            $grouped[$key]['tokensCacheWrite'] += (int) $row['tokensCacheWrite'];
            $grouped[$key]['cost'] = $grouped[$key]['cost']->add(Decimal::fromString((string) $row['costUsd']));
        }

        $rows = array_values($grouped);

        // Лексикографическая сортировка по байтам: не зависит от коллации БД.
        usort($rows, static function (array $a, array $b): int {
            return strcmp($a['user'], $b['user']) ?: strcmp($a['model'], $b['model']);
        });

        $totals = [
            'tokensInput' => 0,
            'tokensOutput' => 0,
            'tokensCacheRead' => 0,
            'tokensCacheWrite' => 0,
            'cost' => Decimal::zero(),
        ];

        foreach ($rows as $row) {
            $totals['tokensInput'] += $row['tokensInput'];
            $totals['tokensOutput'] += $row['tokensOutput'];
            $totals['tokensCacheRead'] += $row['tokensCacheRead'];
            $totals['tokensCacheWrite'] += $row['tokensCacheWrite'];
            $totals['cost'] = $totals['cost']->add($row['cost']);
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * @param array{rows: list<array{user: string, model: string, tokensInput: int, tokensOutput: int, tokensCacheRead: int, tokensCacheWrite: int, cost: Decimal}>, totals: array{tokensInput: int, tokensOutput: int, tokensCacheRead: int, tokensCacheWrite: int, cost: Decimal}} $report
     */
    public function toCsv(array $report): string
    {
        $lines = [self::HEADER];

        foreach ($report['rows'] as $row) {
            $lines[] = implode(',', [
                self::escape($row['user']),
                self::escape($row['model']),
                (string) $row['tokensInput'],
                (string) $row['tokensOutput'],
                (string) $row['tokensCacheRead'],
                (string) $row['tokensCacheWrite'],
                $row['cost']->fixed(6),
            ]);
        }

        $totals = $report['totals'];
        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $totals['tokensInput'],
            (string) $totals['tokensOutput'],
            (string) $totals['tokensCacheRead'],
            (string) $totals['tokensCacheWrite'],
            $totals['cost']->fixed(6),
        ]);

        return implode("\n", $lines) . "\n";
    }

    private static function escape(string $value): string
    {
        if (strpbrk($value, ",\"\n\r") === false) {
            return $value;
        }

        return '"' . str_replace('"', '""', $value) . '"';
    }
}
