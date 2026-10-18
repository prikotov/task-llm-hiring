<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Сборка CSV-отчёта из строк рассчитанных событий — чистая функция.
 *
 * Правила:
 *  - заголовок строго по спецификации;
 *  - строки — по одной на пару (user, model), model задан как <provider>/<code>;
 *  - сортировка по user, затем по model — лексикографически (strcmp), по возрастанию;
 *  - cost_usd — сумма уже округлённых до 6 знаков стоимостей событий, ровно 6 знаков;
 *  - итоговая строка TOTAL с пустым полем model;
 *  - разделитель «,», переводы строк «\n», кодировка UTF-8;
 *  - поля с «,», «"» или переводами строк экранируются по RFC 4180 (см. допущения).
 */
final class ReportBuilder
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /**
     * @param list<ReportRow> $rows
     */
    public function build(array $rows): string
    {
        $groups = [];
        foreach ($rows as $row) {
            $model = $row->provider.'/'.$row->model;
            $key = $row->user."\x1f".$model;

            $group = $groups[$key] ?? ['user' => $row->user, 'model' => $model, 'ti' => '0', 'to' => '0', 'cr' => '0', 'cw' => '0', 'cost' => '0'];
            $group['ti'] = Money::add($group['ti'], (string) $row->tokensInput);
            $group['to'] = Money::add($group['to'], (string) $row->tokensOutput);
            $group['cr'] = Money::add($group['cr'], (string) $row->tokensCacheRead);
            $group['cw'] = Money::add($group['cw'], (string) $row->tokensCacheWrite);
            $group['cost'] = Money::add($group['cost'], $row->costUsd);
            $groups[$key] = $group;
        }

        $list = array_values($groups);
        usort($list, static fn (array $a, array $b): int => \strcmp($a['user'], $b['user']) ?: \strcmp($a['model'], $b['model']));

        $lines = [self::HEADER];
        $totals = ['ti' => '0', 'to' => '0', 'cr' => '0', 'cw' => '0', 'cost' => '0'];
        foreach ($list as $g) {
            $lines[] = implode(',', [
                self::csvCell($g['user']),
                self::csvCell($g['model']),
                self::intCell($g['ti']),
                self::intCell($g['to']),
                self::intCell($g['cr']),
                self::intCell($g['cw']),
                Money::format($g['cost']),
            ]);
            $totals['ti'] = Money::add($totals['ti'], $g['ti']);
            $totals['to'] = Money::add($totals['to'], $g['to']);
            $totals['cr'] = Money::add($totals['cr'], $g['cr']);
            $totals['cw'] = Money::add($totals['cw'], $g['cw']);
            $totals['cost'] = Money::add($totals['cost'], $g['cost']);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            self::intCell($totals['ti']),
            self::intCell($totals['to']),
            self::intCell($totals['cr']),
            self::intCell($totals['cw']),
            Money::format($totals['cost']),
        ]);

        return implode("\n", $lines)."\n";
    }

    private static function csvCell(string $value): string
    {
        if (preg_match('/[",\r\n]/', $value) === 1) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }

    /** Количество токенов — целое, без хвоста после точки. */
    private static function intCell(string $value): string
    {
        return Money::format($value, 0);
    }
}
