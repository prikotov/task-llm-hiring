<?php

declare(strict_types=1);

namespace App\Domain;

use App\Exception\ImportException;

/**
 * Разбор и валидация JSONL-файла событий использования.
 *
 * - каждая непустая строка — объект события;
 * - provider/provider_request_id/model/user — непустые строки;
 * - ts — ISO-8601 UTC с суффиксом Z;
 * - tokens_* — целые >= 0 (отсутствующее поле трактуется как 0).
 */
final class UsageParser
{
    private const ISO_UTC_TS = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/';

    /**
     * @return list<UsageEventRow>
     */
    public function parseFile(string $raw): array
    {
        $rows = [];
        $lines = explode("\n", $raw);

        foreach ($lines as $index => $line) {
            $line = rtrim($line, "\r");
            if (trim($line) === '') {
                continue;
            }

            try {
                $rows[] = $this->parseLine($line);
            } catch (ImportException $e) {
                throw new ImportException('Строка ' . ($index + 1) . ': ' . $e->getMessage());
            }
        }

        return $rows;
    }

    public function parseLine(string $line): UsageEventRow
    {
        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException('некорректный JSON: ' . $e->getMessage());
        }

        if (!is_array($data) || array_is_list($data)) {
            throw new ImportException('событие должно быть JSON-объектом');
        }

        $provider = self::nonEmptyString($data['provider'] ?? null, 'provider');
        $providerRequestId = self::nonEmptyString($data['provider_request_id'] ?? null, 'provider_request_id');
        $model = self::nonEmptyString($data['model'] ?? null, 'model');
        $user = self::nonEmptyString($data['user'] ?? null, 'user');
        $ts = self::parseDateTime($data['ts'] ?? null);

        return new UsageEventRow(
            $provider,
            $providerRequestId,
            $model,
            $user,
            $ts,
            self::tokensCount($data['tokens_input'] ?? null, 'tokens_input'),
            self::tokensCount($data['tokens_output'] ?? null, 'tokens_output'),
            self::tokensCount($data['tokens_cache_read'] ?? null, 'tokens_cache_read'),
            self::tokensCount($data['tokens_cache_write'] ?? null, 'tokens_cache_write'),
        );
    }

    /**
     * @param mixed $value
     */
    private static function nonEmptyString($value, string $field): string
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException("поле '{$field}': ожидается непустая строка");
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    private static function tokensCount($value, string $field): int
    {
        if ($value === null) {
            return 0;
        }

        if (!is_int($value) || $value < 0) {
            throw new ImportException("поле '{$field}': ожидается целое число >= 0");
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    private static function parseDateTime($value): \DateTimeImmutable
    {
        if (!is_string($value) || preg_match(self::ISO_UTC_TS, $value) !== 1) {
            throw new ImportException("поле 'ts': ожидается ISO-8601 UTC с суффиксом Z (2026-09-15T11:00:00Z)");
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ImportException("поле 'ts': некорректная дата — " . $e->getMessage());
        }
    }
}
