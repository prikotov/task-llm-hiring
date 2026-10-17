<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\Iso8601;
use App\Domain\TokenUsage;
use App\Exception\ImportException;

/**
 * Парсинг JSONL-файла событий использования.
 *
 * Правила:
 *  - одна строка — одно событие; пустые строки пропускаются;
 *  - некорректная строка — ImportException (команда завершится кодом 1),
 *    в сообщении указан номер строки;
 *  - дедупликация (provider, provider_request_id) — в пределах файла
 *    и с уже загруженными событиями (первое вхождение побеждает).
 */
final class UsageFileParser
{
    /**
     * @return list<UsageEventInput>
     *
     * @throws ImportException
     */
    public static function parseFile(string $path): array
    {
        if (!is_file($path)) {
            throw new ImportException('Файл не найден: ' . $path);
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException('Не удалось прочитать файл: ' . $path);
        }

        $lines = preg_split('/\R+/', $raw) ?: [];
        $seen = [];
        $result = [];

        foreach ($lines as $index => $line) {
            if (trim($line) === '') {
                continue;
            }

            $input = self::parseLine($line, $index + 1);
            $key = $input->deduplicationKey();
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = $input;
        }

        return $result;
    }

    /**
     * @throws ImportException
     */
    private static function parseLine(string $line, int $lineNumber): UsageEventInput
    {
        $ctx = 'строка ' . $lineNumber;

        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException($ctx . ': некорректный JSON: ' . $e->getMessage());
        }

        if (!is_array($data)) {
            throw new ImportException($ctx . ': ожидался JSON-объект');
        }

        $provider = self::string($data, 'provider', $ctx);
        $requestId = self::string($data, 'provider_request_id', $ctx);
        $model = self::string($data, 'model', $ctx);
        $user = self::string($data, 'user', $ctx);

        $tsRaw = $data['ts'] ?? null;
        if (!is_string($tsRaw)) {
            throw new ImportException($ctx . '.ts: ожидался ISO-8601 timestamp (UTC)');
        }

        try {
            $ts = Iso8601::parseUtc($tsRaw);
        } catch (\ValueError $e) {
            throw new ImportException($ctx . '.ts: ' . $e->getMessage());
        }

        $tokens = new TokenUsage(
            self::tokenCount($data, 'tokens_input', $ctx),
            self::tokenCount($data, 'tokens_output', $ctx),
            self::tokenCount($data, 'tokens_cache_read', $ctx),
            self::tokenCount($data, 'tokens_cache_write', $ctx),
        );

        return new UsageEventInput($provider, $requestId, $model, $user, $ts, $tokens);
    }

    /**
     * Количество токенов: целое неотрицательное (JSON-число).
     * Отсутствие поля трактуется как 0.
     *
     * @param array<mixed> $data
     *
     * @throws ImportException
     */
    private static function tokenCount(array $data, string $field, string $ctx): int
    {
        if (!array_key_exists($field, $data)) {
            return 0;
        }

        $value = $data[$field];

        if (is_int($value)) {
            if ($value < 0) {
                throw new ImportException($ctx . '.' . $field . ': целое должно быть >= 0');
            }

            return $value;
        }

        if (is_float($value) && $value >= 0 && floor($value) === $value && $value <= PHP_INT_MAX) {
            return (int) $value; // JSON-число без дробной части вне диапазона int
        }

        if (is_string($value) && preg_match('/^\d+$/', $value) !== 0) {
            return (int) $value;
        }

        throw new ImportException($ctx . '.' . $field . ': ожидалось целое число >= 0');
    }

    /**
     * @param array<mixed> $data
     *
     * @throws ImportException
     */
    private static function string(array $data, string $field, string $ctx): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException($ctx . '.' . $field . ': ожидалась непустая строка');
        }

        return $value;
    }
}
