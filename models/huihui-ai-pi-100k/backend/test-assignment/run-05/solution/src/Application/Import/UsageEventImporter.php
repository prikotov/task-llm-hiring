<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\Exception\InputException;
use App\Support\IsoUtcTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Импорт событий использования (команда app:usage:import).
 *
 * Правило идемпотентности (TASK.md, п. 5): события дедуплицируются
 * по (provider, provider_request_id) — повторная доставка уже известного
 * события целиком игнорируется. Реализовано INSERT ... ON CONFLICT
 * DO NOTHING: атомарно на строку и на весь файл (транзакция).
 *
 * @throws InputException
 */
final class UsageEventImporter
{
    private const INSERT_SQL = <<<SQL
        INSERT INTO usage_events
            (provider, request_id, model, "user", ts,
             tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
             status)
        VALUES (:provider, :request_id, :model, :user, :ts,
             :tokens_input, :tokens_output, :tokens_cache_read, :tokens_cache_write,
             'unrated')
        ON CONFLICT (provider, request_id) DO NOTHING
        SQL;

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @throws InputException
     */
    public function import(string $path): ImportResult
    {
        $raw = $this->readFile($path);
        $records = $this->parseLines($raw);

        $inserted = 0;
        $this->connection->transactional(function () use ($records, &$inserted): void {
            foreach ($records as $record) {
                $inserted += (int) $this->connection->executeStatement(
                    self::INSERT_SQL,
                    [
                        'provider' => $record->provider,
                        'request_id' => $record->requestId,
                        'model' => $record->model,
                        'user' => $record->user,
                        'ts' => $record->ts->format('Y-m-d H:i:s'),
                        'tokens_input' => $record->tokensInput,
                        'tokens_output' => $record->tokensOutput,
                        'tokens_cache_read' => $record->tokensCacheRead,
                        'tokens_cache_write' => $record->tokensCacheWrite,
                    ],
                );
            }
        });

        return new ImportResult(fileLines: count($records), inserted: $inserted);
    }

    private function readFile(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InputException(sprintf('Файл не найден или нечитаем: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new InputException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        return $raw;
    }

    /**
     * @return UsageEventRecord[]
     */
    private function parseLines(string $raw): array
    {
        $records = [];
        foreach (
            array_values(array_filter(
                explode("\n", $raw),
                static fn (string $line): bool => trim($line) !== '',
            )) as $line
        ) {
            $records[] = $this->parseLine($line);
        }

        return $records;
    }

    /**
     * @throws InputException
     */
    private function parseLine(string $line): UsageEventRecord
    {
        /** @var array<mixed>|null $data */
        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputException(sprintf('Некорректная строка JSONL: %s', $e->getMessage()), 0, $e);
        }

        if (!is_array($data)) {
            throw new InputException('Строка JSONL должна быть объектом');
        }

        $provider = self::requireNonEmptyString($data, 'provider', 64);
        $requestId = self::requireNonEmptyString($data, 'provider_request_id', 255);
        $model = self::requireNonEmptyString($data, 'model', 64);
        $user = self::requireNonEmptyString($data, 'user', 255);

        $tsRaw = $data['ts'] ?? null;
        if (!is_string($tsRaw) || $tsRaw === '') {
            throw new InputException('Поле "ts" — строка времени ISO-8601 UTC');
        }
        $ts = IsoUtcTime::parse($tsRaw, 'поле "ts"');

        return new UsageEventRecord(
            provider: $provider,
            requestId: $requestId,
            model: $model,
            user: $user,
            ts: $ts,
            tokensInput: self::requireTokenCount($data, 'tokens_input'),
            tokensOutput: self::requireTokenCount($data, 'tokens_output'),
            tokensCacheRead: self::requireTokenCount($data, 'tokens_cache_read'),
            tokensCacheWrite: self::requireTokenCount($data, 'tokens_cache_write'),
        );
    }

    /**
     * @param array<mixed> $data
     */
    private static function requireNonEmptyString(array $data, string $key, int $maxLen): string
    {
        $v = $data[$key] ?? null;
        if (!is_string($v) || $v === '' || strlen($v) > $maxLen) {
            throw new InputException(sprintf('Поле "%s" — непустая строка (не более %d символов)', $key, $maxLen));
        }

        return $v;
    }

    /**
     * @param array<mixed> $data
     */
    private static function requireTokenCount(array $data, string $key): int
    {
        $v = $data[$key] ?? 0;
        if (!is_int($v) || $v < 0) {
            throw new InputException(sprintf('Поле "%s" — целое число >= 0', $key));
        }

        return $v;
    }
}
