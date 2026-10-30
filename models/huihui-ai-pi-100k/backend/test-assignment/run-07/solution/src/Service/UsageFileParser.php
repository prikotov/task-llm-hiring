<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UsageEventRow;
use App\Domain\IsoUtc;
use App\Exception\DataImportException;

/**
 * Парсинг и строгая валидация JSONL-файла событий использования.
 */
final class UsageFileParser
{
    /**
     * @return list<UsageEventRow>
     */
    public function parseFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new DataImportException(sprintf('Файл не найден или не читается: "%s"', $path));
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new DataImportException(sprintf('Не удалось прочитать файл: "%s"', $path));
        }

        $result = [];
        foreach ($lines as $no => $line) {
            $line = trim($line);
            if ($no === 0) {
                // Допускаем BOM в начале файла.
                $line = preg_replace('/^\xEF\xBB\xBF/', '', $line) ?? $line;
            }
            if ($line === '') {
                continue;
            }

            $result[] = self::parseLine($line, $no + 1);
        }

        return $result;
    }

    public function parseLine(string $json, int $lineNo): UsageEventRow
    {
        $at = sprintf('строка %d', $lineNo);
        $raw = json_decode($json, true);
        if (!is_array($raw)) {
            throw new DataImportException(sprintf('%s: ожидается JSON-объект', $at));
        }

        $provider = self::string($raw, 'provider', $at, 64);
        $pid = self::string($raw, 'provider_request_id', $at, 255);
        $model = self::string($raw, 'model', $at, 64);
        $user = self::string($raw, 'user', $at, 255);
        $tsRaw = self::string($raw, 'ts', $at, 40);
        $ts = IsoUtc::parse($tsRaw);

        return new UsageEventRow(
            $provider,
            $pid,
            $model,
            $user,
            $ts,
            self::token($raw, 'tokens_input', $at),
            self::token($raw, 'tokens_output', $at),
            self::token($raw, 'tokens_cache_read', $at),
            self::token($raw, 'tokens_cache_write', $at),
        );
    }

    /**
     * @param array<string, mixed> $raw
     */
    private static function string(array $raw, string $field, string $at, int $maxLen): string
    {
        if (!array_key_exists($field, $raw)) {
            throw new DataImportException(sprintf('%s: отсутствует поле "%s"', $at, $field));
        }

        $value = $raw[$field];
        if (!is_string($value) || $value === '') {
            throw new DataImportException(sprintf('%s: поле "%s" — непустая строка', $at, $field));
        }
        if (mb_strlen($value) > $maxLen) {
            throw new DataImportException(sprintf('%s: поле "%s" — до %d символов', $at, $field, $maxLen));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $raw
     */
    private static function token(array $raw, string $field, string $at): int
    {
        if (!array_key_exists($field, $raw)) {
            throw new DataImportException(sprintf('%s: отсутствует поле "%s"', $at, $field));
        }

        $value = $raw[$field];
        if (!is_int($value) || $value < 0) {
            throw new DataImportException(sprintf('%s: поле "%s" — целое ≥ 0', $at, $field));
        }
        if ($value > 9_223_372_036_854_775_807) {
            throw new DataImportException(sprintf('%s: поле "%s" превышает диапазон bigint', $at, $field));
        }

        return $value;
    }
}
