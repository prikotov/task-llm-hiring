<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UsageEventRow;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

/**
 * Приём событий использования: идемпотентная вставка по естественному ключу
 * `(provider, provider_request_id)`. Повторная доставка уже известного
 * события — полная дубликат (ничего не обновляется, ничего не доначисляется).
 */
final class UsageIngestor
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @param list<UsageEventRow> $rows
     *
     * @return array{inserted: int, duplicates: int}
     */
    public function import(array $rows): array
    {
        $sql = 'INSERT INTO usage_events
                   (provider_code,
                    provider_request_id,
                    model_code,
                    user_id,
                    ts,
                    tokens_input,
                    tokens_output,
                    tokens_cache_read,
                    tokens_cache_write)
                VALUES
                   (:provider_code,
                    :provider_request_id,
                    :model_code,
                    :user_id,
                    :ts,
                    :tokens_input,
                    :tokens_output,
                    :tokens_cache_read,
                    :tokens_cache_write)
                ON CONFLICT (provider_code, provider_request_id)
                DO NOTHING';

        $types = [
            'ts' => Types::DATETIMETZ_IMMUTABLE,
            'tokens_input' => Types::BIGINT,
            'tokens_output' => Types::BIGINT,
            'tokens_cache_read' => Types::BIGINT,
            'tokens_cache_write' => Types::BIGINT,
        ];

        $inserted = 0;

        $this->connection->transactional(function () use ($sql, $types, $rows, &$inserted): void {
            foreach ($rows as $row) {
                $affected = $this->connection->executeStatement(
                    $sql,
                    [
                        'provider_code' => $row->provider,
                        'provider_request_id' => $row->providerRequestId,
                        'model_code' => $row->model,
                        'user_id' => $row->user,
                        'ts' => $row->ts,
                        'tokens_input' => $row->tokensInput,
                        'tokens_output' => $row->tokensOutput,
                        'tokens_cache_read' => $row->tokensCacheRead,
                        'tokens_cache_write' => $row->tokensCacheWrite,
                    ],
                    $types
                );
                if ($affected > 0) {
                    $inserted++;
                }
            }
        });

        return [
            'inserted' => $inserted,
            'duplicates' => \count($rows) - $inserted,
        ];
    }
}
