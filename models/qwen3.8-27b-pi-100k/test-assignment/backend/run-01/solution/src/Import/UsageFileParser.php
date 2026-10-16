<?php

declare(strict_types=1);

namespace App\Import;

use App\Support\Iso8601;

/**
 * Разбор JSONL-файла событий использования (по строке на событие).
 * Пустые строки (включая перевод строки в конце файла) пропускаются.
 * Любая некорректная строка — ImportException → код возврата 1
 * (до вставки что-либо в БД: файл проверяется целиком).
 */
final class UsageFileParser
{
    public function parseFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл не найден или недоступен для чтения: "%s"', $path));
        }
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new ImportException(sprintf('Не удалось открыть файл: "%s"', $path));
        }

        try {
            $specs = [];
            $line = 0;
            while (($raw = fgets($handle)) !== false) {
                $line++;
                if (trim($raw) === '') {
                    continue;
                }
                $specs[] = $this->parseLine(trim($raw), $line);
            }

            return $specs;
        } finally {
            fclose($handle);
        }
    }

    public function parseLine(string $line, int $lineNo): UsageSpec
    {
        $data = json_decode($line, true);
        if (!is_array($data)) {
            throw new ImportException(sprintf('Строка %d: некорректный JSON', $lineNo));
        }

        $provider = $this->nonEmptyString($data['provider'] ?? null, $lineNo, 'provider', 64);
        $requestId = $this->nonEmptyString($data['provider_request_id'] ?? null, $lineNo, 'provider_request_id', 255);
        $model = $this->nonEmptyString($data['model'] ?? null, $lineNo, 'model', 64);
        $user = $this->nonEmptyString($data['user'] ?? null, $lineNo, 'user', 255);

        try {
            $ts = Iso8601::parseUtc((string) ($data['ts'] ?? ''));
        } catch (\InvalidArgumentException) {
            throw new ImportException(sprintf('Строка %d: "ts" ожидался ISO-8601 datetime в UTC', $lineNo));
        }

        return new UsageSpec(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $this->tokenCount($data['tokens_input'] ?? 0, $lineNo, 'tokens_input'),
            $this->tokenCount($data['tokens_output'] ?? 0, $lineNo, 'tokens_output'),
            $this->tokenCount($data['tokens_cache_read'] ?? 0, $lineNo, 'tokens_cache_read'),
            $this->tokenCount($data['tokens_cache_write'] ?? 0, $lineNo, 'tokens_cache_write'),
        );
    }

    /** @param mixed $value */
    private function nonEmptyString(mixed $value, int $lineNo, string $field, int $maxLength): string
    {
        if (!is_string($value) || $value === '' || mb_strlen($value) > $maxLength) {
            throw new ImportException(
                sprintf('Строка %d: поле "%s" — ожидается непустая строка (не длиннее %d символов)', $lineNo, $field, $maxLength),
            );
        }

        return $value;
    }

    /** @param mixed $value */
    private function tokenCount(mixed $value, int $lineNo, string $field): int
    {
        if (is_int($value) && $value >= 0) {
            return $value;
        }
        if (is_string($value) && preg_match('/^\d{1,18}$/', $value)) {
            return (int) $value;
        }

        throw new ImportException(
            sprintf('Строка %d: поле "%s" — ожидается целое число ≥ 0', $lineNo, $field),
        );
    }
}
