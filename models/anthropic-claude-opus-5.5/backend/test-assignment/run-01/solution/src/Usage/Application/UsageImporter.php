<?php

declare(strict_types=1);

namespace App\Usage\Application;

use Doctrine\DBAL\Connection;

/**
 * Приём событий с дедупликацией по (provider, provider_request_id).
 * Дедупликация выполняется на уровне БД (уникальный индекс + ON CONFLICT DO NOTHING),
 * поэтому устойчива и к дублям внутри файла, и к конкурентным загрузкам.
 */
final class UsageImporter
{
    private const int BATCH = 500;

    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * Сначала валидирует весь файл, затем вставляет в одной транзакции: некорректная строка
     * не приводит к частичной загрузке.
     *
     * @param iterable<string> $lines
     * @return array{inserted: int, duplicates: int}
     */
    public function import(iterable $lines): array
    {
        $events = [];
        $n = 0;
        foreach ($lines as $line) {
            ++$n;
            if (trim($line) === '') {
                continue;
            }
            $events[] = UsageLine::parse($line, $n);
        }

        $inserted = 0;
        $this->db->transactional(function () use ($events, &$inserted): void {
            foreach (array_chunk($events, self::BATCH) as $chunk) {
                $inserted += $this->insertBatch($chunk);
            }
        });

        return ['inserted' => $inserted, 'duplicates' => count($events) - $inserted];
    }

    /** @param list<UsageLine> $chunk */
    private function insertBatch(array $chunk): int
    {
        $values = [];
        $params = [];
        foreach ($chunk as $e) {
            $values[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?)';
            array_push(
                $params,
                $e->provider,
                $e->providerRequestId,
                $e->model,
                $e->user,
                $e->ts->format('Y-m-d H:i:s.uP'),
                $e->tokens->input,
                $e->tokens->output,
                $e->tokens->cacheRead,
                $e->tokens->cacheWrite,
            );
        }
        if ($values === []) {
            return 0;
        }

        $sql = 'INSERT INTO usage_event (provider_code, provider_request_id, model_code, user_id, ts,'
            .' tokens_input, tokens_output, tokens_cache_read, tokens_cache_write) VALUES '
            .implode(', ', $values)
            .' ON CONFLICT (provider_code, provider_request_id) DO NOTHING';

        return (int) $this->db->executeStatement($sql, $params);
    }
}
