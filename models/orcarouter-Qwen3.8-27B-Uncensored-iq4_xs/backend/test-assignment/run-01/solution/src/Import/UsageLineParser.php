<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\UsageEvent;
use App\Exception\ImportException;
use App\Util\UtcTime;

/**
 * Разбор одной строки JSONL файла событий использования.
 *
 * Дедупликация по (provider, provider_request_id) выполняется при
 * импорте; здесь — только валидация.
 */
final class UsageLineParser
{
    /** @param array<string, mixed> $data */
    public static function parse(array $data, string $context): UsageEvent
    {
        $provider = self::nonEmptyString($data, 'provider', $context);
        $requestId = self::nonEmptyString($data, 'provider_request_id', $context);
        $model = self::nonEmptyString($data, 'model', $context);
        $user = self::nonEmptyString($data, 'user', $context);
        $ts = UtcTime::parse($data['ts'] ?? null, $context.'.ts');

        $tokens = [];
        foreach (UsageEvent::TOKEN_FIELDS as $field) {
            $value = $data[$field] ?? null;
            if (!\is_int($value) || $value < 0) {
                throw new ImportException(sprintf('%s: поле "%s" — целое число >= 0', $context, $field));
            }
            $tokens[$field] = $value;
        }

        return new UsageEvent(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $tokens['tokens_input'],
            $tokens['tokens_output'],
            $tokens['tokens_cache_read'],
            $tokens['tokens_cache_write'],
        );
    }

    /** @param array<string, mixed> $item */
    private static function nonEmptyString(array $item, string $field, string $context): string
    {
        $value = $item[$field] ?? null;
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: поле "%s" — обязательная непустая строка', $context, $field));
        }

        return $value;
    }
}
