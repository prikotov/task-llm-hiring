<?php

declare(strict_types=1);

namespace App\Application;

use App\Exception\ImportException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Импорт событий использования (app:usage:import) с дедупликацией
 * по естественному ключу (provider, provider_request_id).
 * Повторные доставки — целиком игнорируются (без обновлений и доначислений).
 */
final class UsageImportService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @throws ImportException
     * @throws DbalException
     *
     * @return array{imported: int, duplicates: int}
     */
    public function import(string $file): array
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new ImportException(sprintf('file not found: %s', $file));
        }

        $lines = file($file, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new ImportException(sprintf('cannot read file: %s', $file));
        }

        $imported = 0;
        $duplicates = 0;

        $conn = $this->connection;
        $conn->transactional(function () use ($conn, $lines, &$imported, &$duplicates) {
            foreach ($lines as $lineNo => $line) {
                $event = self::parseLine($line, $lineNo + 1);

                $affected = $conn->executeStatement(
                    'INSERT INTO usage_events
                        (provider_code, provider_request_id, model_code, user_id, ts,
                         tokens_input, tokens_output, tokens_cache_read, tokens_cache_write)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                     ON CONFLICT (provider_code, provider_request_id) DO NOTHING',
                    [
                        $event['provider'],
                        $event['provider_request_id'],
                        $event['model'],
                        $event['user'],
                        $event['ts'],
                        $event['tokens']['input'],
                        $event['tokens']['output'],
                        $event['tokens']['cache_read'],
                        $event['tokens']['cache_write'],
                    ],
                );

                if ($affected > 0) {
                    ++$imported;
                } else {
                    ++$duplicates;
                }
            }
        });

        return ['imported' => $imported, 'duplicates' => $duplicates];
    }

    /**
     * @throws ImportException
     *
     * @return array{provider: string, provider_request_id: string, model: string,
     *               user: string, ts: string, tokens: array<string,int>}
     */
    private static function parseLine(string $line, int $lineNo): array
    {
        $raw = json_decode($line, true, 512, \JSON_THROW_ON_ERROR);
        if (!\is_array($raw)) {
            throw new ImportException(sprintf('line %d: JSON root must be an object', $lineNo));
        }

        $event = [
            'provider' => self::requireString($raw, 'provider', $lineNo),
            'provider_request_id' => self::requireString($raw, 'provider_request_id', $lineNo),
            'model' => self::requireString($raw, 'model', $lineNo),
            'user' => self::requireString($raw, 'user', $lineNo),
        ];

        $tsRaw = $raw['ts'] ?? null;
        if (!\is_string($tsRaw) || $tsRaw === '') {
            throw new ImportException(sprintf('line %d: ts must be an ISO-8601 string', $lineNo));
        }
        try {
            $ts = new DateTimeImmutable($tsRaw);
        } catch (\Exception) {
            throw new ImportException(sprintf('line %d: ts is not a valid ISO-8601 date "%s"', $lineNo, $tsRaw));
        }
        // Хранение в UTC с точностью до секунды (микросекунды событий не участвуют в тарификации).
        $event['ts'] = $ts
            ->setMicrosecond(0)
            ->setTimezone(new DateTimeZone('UTC'))
            ->format('Y-m-d H:i:s');

        $tokens = [];
        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $field) {
            $v = $raw[$field] ?? null;
            if (!\is_int($v) || $v < 0) {
                throw new ImportException(sprintf('line %d: %s must be an integer >= 0', $lineNo, $field));
            }
            $tokens[substr($field, 7)] = $v;
        }
        $event['tokens'] = $tokens;

        return $event;
    }

    private static function requireString(array $source, string $key, int $lineNo): string
    {
        $v = $source[$key] ?? null;
        if (!\is_string($v) || $v === '') {
            throw new ImportException(sprintf('line %d: %s must be a non-empty string', $lineNo, $key));
        }

        return $v;
    }
}
