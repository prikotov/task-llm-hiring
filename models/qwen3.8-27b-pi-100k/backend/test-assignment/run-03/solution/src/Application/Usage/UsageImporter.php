<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\Usage\EventStatus;
use Doctrine\DBAL\Connection;

/**
 * Приём событий использования (JSONL).
 *
 * Дедупликация — INSERT ... ON CONFLICT (provider_code, provider_request_id)
 * DO NOTHING: повторная доставка известного события целиком игнорируется
 * (никаких обновлений и доначислений), разные провайдеры с одинаковым
 * provider_request_id — разные события. Код выхода 0 даже если все строки — дубли.
 */
final class UsageImporter
{
    private const INSERT_SQL = '
        INSERT INTO usage_events
            (provider_code, provider_request_id, model_code, user_name, ts, ts_raw,
             tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, status, created_at)
        VALUES
            (:provider, :rid, :model, :user, :ts, :ts_raw,
             :tin, :tout, :tcr, :tcw, :status, statement_timestamp())
        ON CONFLICT (provider_code, provider_request_id) DO NOTHING';

    public function __construct(private readonly Connection $connection)
    {
    }

    public function import(string $path): UsageImportSummary
    {
        $events = UsageFile::parse($path);

        $inserted = 0;

        $this->connection->transactional(function () use ($events, &$inserted): void {
            $statement = $this->connection->prepare(self::INSERT_SQL);

            foreach ($events as $event) {
                $statement->bindValue('provider', $event['provider']);
                $statement->bindValue('rid', $event['providerRequestId']);
                $statement->bindValue('model', $event['model']);
                $statement->bindValue('user', $event['user']);
                $statement->bindValue('ts', $event['tsUtc']->format('Y-m-d H:i:s.uP'));
                $statement->bindValue('ts_raw', $event['tsRaw']);
                $statement->bindValue('tin', $event['tokensInput'], \Doctrine\DBAL\ParameterType::INTEGER);
                $statement->bindValue('tout', $event['tokensOutput'], \Doctrine\DBAL\ParameterType::INTEGER);
                $statement->bindValue('tcr', $event['tokensCacheRead'], \Doctrine\DBAL\ParameterType::INTEGER);
                $statement->bindValue('tcw', $event['tokensCacheWrite'], \Doctrine\DBAL\ParameterType::INTEGER);
                $statement->bindValue('status', EventStatus::Pending->value);
                $inserted += (int) $statement->executeStatement();
            }
        });

        return new UsageImportSummary(count($events), $inserted, count($events) - $inserted);
    }
}
