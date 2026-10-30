<?php

declare(strict_types=1);

namespace App\Support;

/** Разбор ISO-8601 для пользовательских опций; результат — UTC. */
final class IsoTime
{
    public static function parse(string $value, string $context): \DateTimeImmutable
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:?\d{2})$/', $value)) {
            throw new ImportError(sprintf('%s: значение "%s" — ожидался ISO-8601 (UTC)', $context, $value));
        }
        try {
            return (new \DateTimeImmutable($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Throwable $e) {
            throw new ImportError(sprintf('%s: значение "%s" не распознано: %s', $context, $value, $e->getMessage()));
        }
    }
}
