<?php

declare(strict_types=1);

namespace App\Import;

/**
 * Парсинг одной строки JSONL с событием использования.
 */
final class UsageEventParser
{
    /**
     * @return array{
     *     provider:string,
     *     providerRequestId:string,
     *     model:string,
     *     user:string,
     *     ts:\DateTimeImmutable,
     *     input:int, output:int, cacheRead:int, cacheWrite:int
     * }
     *
     * @throws ImportException
     */
    public static function parseLine(string $line): array
    {
        $line = trim($line);
        if ($line === '') {
            throw new ImportException('Пустая строка JSONL');
        }

        $data = json_decode($line, true);
        if (!is_array($data)) {
            throw new ImportException('Строка не является корректным JSON: '.$line);
        }

        $provider = self::nonEmpty($data['provider'] ?? null, 'provider');
        $reqId = self::nonEmpty($data['provider_request_id'] ?? null, 'provider_request_id');
        $model = self::nonEmpty($data['model'] ?? null, 'model');
        $user = self::nonEmpty($data['user'] ?? null, 'user');
        $ts = self::parseTs($data['ts'] ?? null);

        return [
            'provider' => $provider,
            'providerRequestId' => $reqId,
            'model' => $model,
            'user' => $user,
            'ts' => $ts,
            'input' => self::nonNegInt($data['tokens_input'] ?? 0, 'tokens_input'),
            'output' => self::nonNegInt($data['tokens_output'] ?? 0, 'tokens_output'),
            'cacheRead' => self::nonNegInt($data['tokens_cache_read'] ?? 0, 'tokens_cache_read'),
            'cacheWrite' => self::nonNegInt($data['tokens_cache_write'] ?? 0, 'tokens_cache_write'),
        ];
    }

    /** @param mixed $value */
    private static function nonEmpty($value, string $field): string
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException('Поле "'.$field.'" отсутствует или пусто');
        }

        return $value;
    }

    /** @param mixed $value */
    private static function parseTs($value): \DateTimeImmutable
    {
        if (!is_string($value)) {
            throw new ImportException('Поле "ts" должно быть строкой ISO-8601 (UTC)');
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value, new \DateTimeZone('UTC'));
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $value, new \DateTimeZone('UTC'));
        }
        if ($dt === false) {
            throw new ImportException('Не удалось разобрать ts: '.$value);
        }

        return $dt;
    }

    /** @param mixed $value */
    private static function nonNegInt($value, string $field): int
    {
        if (is_string($value) && preg_match('/^-?\d+$/', $value) === 1) {
            $value = (int) $value;
        }
        if (is_float($value)) {
            if (floor($value) !== $value || $value < 0) {
                throw new ImportException('Поле "'.$field.'" должно быть целым числом >= 0');
            }
            $value = (int) $value;
        }
        if (!is_int($value) || $value < 0) {
            throw new ImportException('Поле "'.$field.'" должно быть целым числом >= 0');
        }

        return $value;
    }
}
