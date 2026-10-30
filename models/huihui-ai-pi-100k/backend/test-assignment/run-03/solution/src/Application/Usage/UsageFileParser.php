<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Application\Exception\FileFormatException;
use App\Domain\Common\Iso8601Utc;
use Exception;
use JsonException;

/**
 * Разбор и валидация JSONL-файла событий использования.
 *
 * Допущения (фиксированы в PR_DESCRIPTION.md):
 *  — пустые (в т.ч. из одиночного перевода строки) строки пропускаются;
 *  — неизвестные поля событий игнорируются;
 *  — при дубле (provider, provider_request_id) внутри одного файла
 *    учитывается первая запись, последующие — как повторная доставка.
 */
final class UsageFileParser
{
    /** @throws FileFormatException */
    public static function fromFile(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new FileFormatException(sprintf('Не удалось открыть файл "%s"', $path));
        }

        try {
            $specs = [];
            $seen = [];
            $lineNumber = 0;

            while (($line = fgets($handle)) !== false) {
                $lineNumber++;
                if (trim($line) === '') {
                    continue;
                }

                $spec = self::parseLine($line, $lineNumber);
                $key = $spec->provider."\0".$spec->providerRequestId;
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                $specs[] = $spec;
            }

            return $specs;
        } finally {
            fclose($handle);
        }
    }

    /** @throws FileFormatException */
    private static function parseLine(string $line, int $lineNumber): UsageEventSpec
    {
        try {
            $data = json_decode($line, true, 64, JSON_BIGINT_AS_STRING);
        } catch (JsonException $e) {
            throw FileFormatException::invalidFile(sprintf('строка %d', $lineNumber), 'некорректный JSON: '.$e->getMessage());
        }

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw FileFormatException::invalidFile(sprintf('строка %d', $lineNumber), 'некорректный JSON: ожидается объект события');
        }

        $ctx = sprintf('строка %d', $lineNumber);

        $provider = self::nonEmptyString($data, 'provider', $ctx);
        $requestId = self::nonEmptyString($data, 'provider_request_id', $ctx);
        $model = self::nonEmptyString($data, 'model', $ctx);
        $user = self::nonEmptyString($data, 'user', $ctx);

        $tsRaw = $data['ts'] ?? null;
        if (!is_string($tsRaw)) {
            throw FileFormatException::invalidFile($ctx, 'поле "ts" должно быть строкой ISO-8601 (UTC)');
        }
        try {
            $ts = Iso8601Utc::parse($tsRaw);
        } catch (Exception $e) {
            throw FileFormatException::invalidFile($ctx, sprintf('поле "ts": %s', $e->getMessage()));
        }

        $tokensInput = self::tokenCount($data, 'tokens_input', $ctx);
        $tokensOutput = self::tokenCount($data, 'tokens_output', $ctx);
        $tokensCacheRead = self::tokenCount($data, 'tokens_cache_read', $ctx);
        $tokensCacheWrite = self::tokenCount($data, 'tokens_cache_write', $ctx);

        return new UsageEventSpec(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $tokensInput,
            $tokensOutput,
            $tokensCacheRead,
            $tokensCacheWrite,
        );
    }

    private static function nonEmptyString(array $data, string $field, string $ctx): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть непустой строкой', $field));
        }

        return $value;
    }

    /** Целое неотрицательное количество токенов (JSON-целое или decimal-строка для bigint). */
    private static function tokenCount(array $data, string $field, string $ctx): int
    {
        $value = $data[$field] ?? null;

        if (is_string($value)) {
            if (preg_match('/^\d{1,18}$/', $value) === 1) {
                return (int) $value;
            }
            throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть целым ≥ 0, а получено "%s"', $field, $value));
        }
        if (is_int($value)) {
            if ($value < 0) {
                throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть ≥ 0', $field));
            }
            return $value;
        }
        if (is_float($value) && (int) $value === $value && $value >= 0 && $value < PHP_INT_MAX) {
            // JSON-число без запятой PHP может отдать float при больших значениях — принимаем только точные целые.
            return (int) $value;
        }

        throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть целым ≥ 0', $field));
    }
}
