<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Парсер событий использования (JSONL).
 *
 * Одна строка JSON — одно событие.
 *
 * Возвращает массив строк в каноническом виде:
 *   [
 *     'provider' => string,
 *     'provider_request_id' => string,
 *     'model' => string,
 *     'user' => string,
 *     'ts' => string (ISO-8601 UTC),
 *     'tokens_input' => int,
 *     'tokens_output' => int,
 *     'tokens_cache_read' => int,
 *     'tokens_cache_write' => int,
 *   ]
 *
 * Ошибки: строка не JSON / не объект / отсутствуют обязательные поля /
 * неверный тип / token-счётчик отрицательный → ImportException (строка N).
 */
final class UsageParser
{
    /**
     * @param string $raw JSONL
     * @return array<int, array<string, mixed>>
     *
     * @throws ImportException
     */
    public function parse(string $raw): array
    {
        $out = [];
        $lines = preg_split('/\R/', $raw);
        foreach ($lines as $idx => $line) {
            if (trim($line) === '') {
                continue;
            }
            $lineNo = $idx + 1;
            $event = json_decode($line, true);
            if (!is_array($event)) {
                throw ImportException::jsonlLine($lineNo);
            }

            $out[] = $this->parseEvent($event, $lineNo);
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>
     *
     * @throws ImportException
     */
    private function parseEvent(array $event, int $lineNo): array
    {
        $provider = $event['provider'] ?? null;
        if (!is_string($provider) || $provider === '') {
            throw ImportException::jsonlLine($lineNo);
        }
        $pid = $event['provider_request_id'] ?? null;
        if (!is_string($pid) || $pid === '') {
            throw ImportException::jsonlLine($lineNo);
        }
        $model = $event['model'] ?? null;
        if (!is_string($model) || $model === '') {
            throw ImportException::jsonlLine($lineNo);
        }
        $user = $event['user'] ?? null;
        if (!is_string($user) || $user === '') {
            throw ImportException::jsonlLine($lineNo);
        }
        $ts = $event['ts'] ?? null;
        if (!is_string($ts) || !self::isValidTs($ts)) {
            throw ImportException::jsonlLine($lineNo);
        }

        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $k) {
            $v = $event[$k] ?? 0;
            if (!is_int($v) || $v < 0) {
                throw ImportException::jsonlLine($lineNo);
            }
        }

        return [
            'provider' => $provider,
            'provider_request_id' => $pid,
            'model' => $model,
            'user' => $user,
            'ts' => $ts,
            'tokens_input' => $event['tokens_input'] ?? 0,
            'tokens_output' => $event['tokens_output'] ?? 0,
            'tokens_cache_read' => $event['tokens_cache_read'] ?? 0,
            'tokens_cache_write' => $event['tokens_cache_write'] ?? 0,
        ];
    }

    private static function isValidTs(string $ts): bool
    {
        return preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(Z)?$/', $ts) === 1;
    }
}
