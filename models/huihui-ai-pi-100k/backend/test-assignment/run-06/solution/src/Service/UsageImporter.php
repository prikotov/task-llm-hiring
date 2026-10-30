<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

final class UsageImporter
{
    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * Import usage events from a JSONL file.
     * Deduplicates by (provider, provider_request_id): existing events are ignored.
     * Returns the number of newly inserted events.
     */
    public function import(string $filePath): int
    {
        $fp = fopen($filePath, 'r');
        if ($fp === false) {
            throw new \RuntimeException("Cannot read file: {$filePath}");
        }

        $inserted = 0;
        $lineNum = 0;
        $this->db->beginTransaction();

        try {
            while (($line = fgets($fp)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $lineNum++;

                $data = json_decode($line, true);
                if ($data === null) {
                    throw new \RuntimeException("Invalid JSON on line {$lineNum}: {$line}");
                }

                $this->validateEvent($data, $lineNum);

                $result = $this->db->executeStatement(
                    'INSERT INTO usage_events
                        (provider_code, provider_request_id, model_code, user_id, ts,
                         tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, status)
                     VALUES (?, ?, ?, ?, ?::timestamptz, ?, ?, ?, ?, ?)
                     ON CONFLICT (provider_code, provider_request_id) DO NOTHING',
                    [
                        $data['provider'],
                        $data['provider_request_id'],
                        $data['model'],
                        $data['user'],
                        $data['ts'],
                        (int) ($data['tokens_input'] ?? 0),
                        (int) ($data['tokens_output'] ?? 0),
                        (int) ($data['tokens_cache_read'] ?? 0),
                        (int) ($data['tokens_cache_write'] ?? 0),
                        'pending',
                    ]
                );

                if ($result > 0) {
                    $inserted++;
                }
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        } finally {
            fclose($fp);
        }

        return $inserted;
    }

    private function validateEvent(array $data, int $lineNum): void
    {
        $required = ['provider', 'provider_request_id', 'model', 'user', 'ts'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \RuntimeException("Missing required field '{$field}' on line {$lineNum}");
            }
        }

        // Validate ts is valid ISO-8601
        try {
            new \DateTimeImmutable($data['ts'], new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \RuntimeException("Invalid timestamp on line {$lineNum}: {$data['ts']}");
        }
    }
}
