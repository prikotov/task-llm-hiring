<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Infrastructure\Import\UsageFileParser;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * Streaming, transactional import. The database unique constraint plus
 * ON CONFLICT DO NOTHING implements idempotent delivery semantics.
 */
final readonly class UsageImportService
{
    private const string INSERT_SQL = <<<'SQL'
        INSERT INTO usage_events (
            provider_code,
            provider_request_id,
            model_code,
            user_id,
            occurred_at,
            tokens_input,
            tokens_output,
            tokens_cache_read,
            tokens_cache_write
        ) VALUES (
            :provider_code,
            :provider_request_id,
            :model_code,
            :user_id,
            :occurred_at,
            :tokens_input,
            :tokens_output,
            :tokens_cache_read,
            :tokens_cache_write
        )
        ON CONFLICT (provider_code, provider_request_id) DO NOTHING
        SQL;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UsageFileParser $parser,
    ) {
    }

    /**
     * @return array{inserted: int, duplicates: int}
     */
    public function importFile(string $path): array
    {
        $connection = $this->entityManager->getConnection();
        $inserted = 0;
        $seen = 0;

        $connection->beginTransaction();

        try {
            foreach ($this->parser->parse($path) as $event) {
                ++$seen;
                $inserted += (int) $connection->executeStatement(
                    self::INSERT_SQL,
                    [
                        'provider_code' => $event->provider,
                        'provider_request_id' => $event->providerRequestId,
                        'model_code' => $event->model,
                        'user_id' => $event->user,
                        'occurred_at' => $event->occurredAt,
                        'tokens_input' => $event->usage->input,
                        'tokens_output' => $event->usage->output,
                        'tokens_cache_read' => $event->usage->cacheRead,
                        'tokens_cache_write' => $event->usage->cacheWrite,
                    ],
                    [
                        'provider_code' => Types::STRING,
                        'provider_request_id' => Types::STRING,
                        'model_code' => Types::STRING,
                        'user_id' => Types::STRING,
                        'occurred_at' => Types::DATETIMETZ_IMMUTABLE,
                        'tokens_input' => Types::BIGINT,
                        'tokens_output' => Types::BIGINT,
                        'tokens_cache_read' => Types::BIGINT,
                        'tokens_cache_write' => Types::BIGINT,
                    ],
                );
            }

            $connection->commit();
        } catch (Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }

        return ['inserted' => $inserted, 'duplicates' => $seen - $inserted];
    }
}
