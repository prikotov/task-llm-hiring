<?php

declare(strict_types=1);

namespace App\Import;

use Doctrine\DBAL\Connection;

/**
 * Загрузка событий использования по правилу идемпотентности (п. 5 ТЗ):
 * повторная доставка (provider, provider_request_id) игнорируется целиком —
 * INSERT ... ON CONFLICT DO NOTHING, никаких обновлений/доначислений.
 * Один файл — один транзакционный пакет вставок.
 */
final class UsageImporter
{
    private const INSERT_SQL = '
        INSERT INTO usage_events (
            provider,
            provider_request_id,
            model,
            user_ref,
            ts,
            tokens_input,
            tokens_output,
            tokens_cache_read,
            tokens_cache_write,
            status
        ) VALUES (
            :provider, :provider_request_id, :model, :user_ref, :ts,
            :tokens_input, :tokens_output, :tokens_cache_read, :tokens_cache_write, :status
        )
        ON CONFLICT (provider, provider_request_id) DO NOTHING';

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @param UsageSpec[] $specs
     * @return int количество новых (вставленных) событий
     */
    public function import(array $specs): int
    {
        $inserted = 0;
        $this->connection->transactional(
            function () use ($specs, &$inserted): void {
                foreach ($specs as $spec) {
                    $inserted += $this->insertOne($spec); // 0 = дубль, 1 = новая строка
                }
            },
        );

        return $inserted;
    }

    private function insertOne(UsageSpec $spec): int
    {
        $rows = $this->connection->executeStatement(self::INSERT_SQL, [
            'provider' => $spec->getProvider(),
            'provider_request_id' => $spec->getProviderRequestId(),
            'model' => $spec->getModel(),
            'user_ref' => $spec->getUser(),
            'ts' => $spec->getTs()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'tokens_input' => $spec->getTokensInput(),
            'tokens_output' => $spec->getTokensOutput(),
            'tokens_cache_read' => $spec->getTokensCacheRead(),
            'tokens_cache_write' => $spec->getTokensCacheWrite(),
            'status' => 'pending',
        ]);

        return (int) $rows;
    }
}
