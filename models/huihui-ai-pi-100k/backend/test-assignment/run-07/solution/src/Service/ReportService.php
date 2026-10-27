<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

/**
 * Сборка отчёта по расходам: group by (user, model), суммирует токен-количества
 * и уже округлённые до 6 знаков стоимости событий. `--from` включительно,
 * `--to` исключительно. Unrated-события в отчёт не попадают никогда.
 */
final class ReportService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /** @return list<ReportRow> */
    public function build(\DateTimeImmutable|null $from, \DateTimeImmutable|null $to): array
    {
        $sql = 'SELECT user_id, provider_code, model_code,
                       tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
                       cost_usd
                FROM usage_events
                WHERE status = :status';
        $params = ['status' => 'rated'];
        $types = [];

        if ($from !== null) {
            $sql .= ' AND ts >= :from';
            $params['from'] = $from;
            $types['from'] = Types::DATETIMETZ_IMMUTABLE;
        }
        if ($to !== null) {
            $sql .= ' AND ts < :to';
            $params['to'] = $to;
            $types['to'] = Types::DATETIMETZ_IMMUTABLE;
        }

        $rows = $this->connection->fetchAllAssociative($sql, $params, $types);

        $groups = [];
        foreach ($rows as $r) {
            $user = (string) $r['user_id'];
            $model = (string) $r['provider_code'] . '/' . (string) $r['model_code'];
            $key = $user . '|' . $model;

            if (!isset($groups[$key])) {
                $groups[$key] = new ReportRow(
                    $user,
                    $model,
                    0,
                    0,
                    0,
                    0,
                    '0'
                );
            }

            $groups[$key] = $groups[$key]->addRow(
                (int) $r['tokens_input'],
                (int) $r['tokens_output'],
                (int) $r['tokens_cache_read'],
                (int) $r['tokens_cache_write'],
                (string) $r['cost_usd']
            );
        }

        $list = array_values($groups);
        usort($list, static function (ReportRow $a, ReportRow $b): int {
            return strcmp($a->user, $b->user) ?: strcmp($a->model, $b->model);
        });

        return $list;
    }
}

/** Строка отчёта (user, model). */
final class ReportRow
{
    /** @var int[] */
    private array $tokens = [0, 0, 0, 0];

    public function __construct(
        public readonly string $user,
        public readonly string $model,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        public readonly string $costUsd,
    ) {
        $this->tokens = [$tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite];
    }

    public function addRow(int $tin, int $tout, int $tcr, int $tcw, string $cost): self
    {
        return new self(
            $this->user,
            $this->model,
            $this->tokens[0] + $tin,
            $this->tokens[1] + $tout,
            $this->tokens[2] + $tcr,
            $this->tokens[3] + $tcw,
            Money::add($this->costUsd, $cost)
        );
    }

    public function tokensInput(): int
    {
        return $this->tokens[0];
    }

    public function tokensOutput(): int
    {
        return $this->tokens[1];
    }

    public function tokensCacheRead(): int
    {
        return $this->tokens[2];
    }

    public function tokensCacheWrite(): int
    {
        return $this->tokens[3];
    }

    /** Форматирует cost_usd ровно до 6 знаков. */
    public function costUsdFormatted(): string
    {
        return Money::to6($this->costUsd);
    }
}
