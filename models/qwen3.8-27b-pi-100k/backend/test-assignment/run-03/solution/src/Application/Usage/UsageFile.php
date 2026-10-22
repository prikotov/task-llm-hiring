<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Shared\InputFormatException;
use App\Shared\IsoUtc;

/**
 * Чтение и полная валидация JSONL-файла событий использования.
 *
 * Файл проверяется целиком до записи: некорректная строка — ошибка
 * (код выхода 1), и при этом ни одна строка не сохраняется.
 * Пустые строки (в т.ч. завершающий перевод строки) не считаются событиями.
 */
final class UsageFile
{
    /** @return list<array<string, mixed>> события, готовые к записи */
    public static function parse(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InputFormatException('файл не найден или недоступен: '.$path);
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InputFormatException('не удалось открыть файл: '.$path);
        }

        $events = [];
        $lineNo = 0;

        while (($line = fgets($handle)) !== false) {
            ++$lineNo;
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $events[] = self::parseLine($trimmed, $path, $lineNo);
        }

        fclose($handle);

        return $events;
    }

    private static function parseLine(string $line, string $path, int $lineNo): array
    {
        $ctx = $path.':'.$lineNo;

        try {
            $data = json_decode($line, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputFormatException($ctx.': некорректный JSON: '.$e->getMessage(), previous: $e);
        }

        if (!\is_array($data) || array_is_list($data)) {
            throw new InputFormatException($ctx.': событие должно быть объектом JSON');
        }

        $tsRaw = self::nonEmptyString($data, 'ts', $ctx);
        $tsUtc = IsoUtc::parse($tsRaw, $ctx.'.ts');

        return [
            'provider' => self::nonEmptyString($data, 'provider', $ctx),
            'providerRequestId' => self::nonEmptyString($data, 'provider_request_id', $ctx),
            'model' => self::nonEmptyString($data, 'model', $ctx),
            'user' => self::nonEmptyString($data, 'user', $ctx),
            'tsUtc' => $tsUtc,
            'tsRaw' => $tsRaw,
            'tokensInput' => self::tokenCount($data, 'tokens_input', $ctx),
            'tokensOutput' => self::tokenCount($data, 'tokens_output', $ctx),
            'tokensCacheRead' => self::tokenCount($data, 'tokens_cache_read', $ctx),
            'tokensCacheWrite' => self::tokenCount($data, 'tokens_cache_write', $ctx),
        ];
    }

    private static function nonEmptyString(array $data, string $key, string $ctx): string
    {
        $value = $data[$key] ?? null;
        if (!\is_string($value) || trim($value) === '') {
            throw new InputFormatException($ctx.": \"{$key}\": требуется непустая строка");
        }

        return $value;
    }

    private static function tokenCount(array $data, string $key, string $ctx): int
    {
        $value = $data[$key] ?? null;
        // Только целое число JSON (int): bool/float/строка — ошибка; отрицательные — ошибка.
        if (!\is_int($value) || $value < 0) {
            throw new InputFormatException($ctx.": \"{$key}\": требуется целое число ≥ 0 (число JSON)");
        }

        return $value;
    }
}
