<?php

declare(strict_types=1);

namespace App\Application\Ingestion;

use App\Domain\DomainException;
use App\Domain\Billing\TokenMix;
use App\Domain\Usage\UsageEvent;

/**
 * Разбор и валидация JSONL-событий использования (импорт).
 *
 * Каждое событие — отдельная строка JSON.
 * Неизвестные поля игнорируются (TASK.md: «лишние поля — игнорировать»).
 *
 * @throws DomainException при невалидной строке или структуре
 */
final class UsageEventsParser
{
    /**
     * @param string[] $lines — строки JSONL (одна на событие)
     * @return UsageEvent[]
     */
    public function parse(array $lines, string $context = 'UsageEventsParser::parse'): array
    {
        $out = [];
        foreach ($lines as $i => $line) {
            if ($line === '' || $line === null) {
                continue; // пустая строка
            }
            $out[] = self::parseOne($line, "{$context}: line {$i}");
        }

        return $out;
    }

    private static function parseOne(string $line, string $ctx): UsageEvent
    {
        $data = json_decode($line, true);
        if (!is_array($data)) {
            throw new DomainException("{$ctx}: not a valid JSON object");
        }

        $provider = self::string($data['provider'] ?? null, "{$ctx}.provider");
        $rid = self::string($data['provider_request_id'] ?? null, "{$ctx}.provider_request_id");
        $model = self::string($data['model'] ?? null, "{$ctx}.model");
        $user = self::string($data['user'] ?? null, "{$ctx}.user", allowEmpty: true);
        $tsStr = self::string($data['ts'] ?? null, "{$ctx}.ts");
        $ts = self::isoUtc($tsStr, "{$ctx}.ts");

        $ti = self::nonNegInt($data['tokens_input'] ?? null, "{$ctx}.tokens_input");
        $to = self::nonNegInt($data['tokens_output'] ?? null, "{$ctx}.tokens_output");
        $tc = self::nonNegInt($data['tokens_cache_read'] ?? null, "{$ctx}.tokens_cache_read");
        $tw = self::nonNegInt($data['tokens_cache_write'] ?? null, "{$ctx}.tokens_cache_write");

        $mix = new TokenMix($ti, $to, $tc, $tw);

        return new UsageEvent(
            provider: $provider,
            providerRequestId: $rid,
            model: $model,
            user: $user,
            ts: $ts,
            tokens: $mix,
        );
    }

    private static function nonNegInt(mixed $v, string $ctx): int
    {
        if ($v === null || $v === '') {
            throw new DomainException("{$ctx}: required but missing");
        }
        if (is_numeric($v) && (int) $v >= 0) {
            return (int) $v;
        }
        if (is_string($v) && preg_match('/^\d+$/', $v)) {
            return (int) $v;
        }
        throw new DomainException("{$ctx}: must be a non-negative integer: \"{$v}\"");
    }

    private static function string(mixed $v, string $ctx, bool $allowEmpty = false): string
    {
        if ($v === null) {
            throw new DomainException("{$ctx}: required but missing");
        }
        if (!is_string($v)) {
            throw new DomainException("{$ctx}: must be a string");
        }
        if (! $allowEmpty && $v === '') {
            throw new DomainException("{$ctx}: must be a non-empty string");
        }

        return $v;
    }

    private static function isoUtc(string $v, string $ctx): \DateTimeImmutable
    {
        if (substr($v, -1) !== 'Z') {
            throw new DomainException("{$ctx}: ISO-8601 UTC (ending with 'Z') required: \"{$v}\"");
        }
        try {
            $date = new \DateTimeImmutable($v);
        } catch (\DateTimeParseException) {
            throw new DomainException("{$ctx}: not a valid ISO-8601 timestamp: \"{$v}\"");
        }
        if ($date->getOffset() !== 0) {
            throw new DomainException("{$ctx}: UTC required (offset 0): \"{$v}\"");
        }

        return $date;
    }

    /**
     * @param string $v — значение, уже проверенное как число/строка
     */
    private static function assertNonNegInt(string $v, string $ctx): void
    {
        if (!preg_match('/^\d+$/', $v)) {
            throw new DomainException("{$ctx}: must be a non-negative integer (or numeric string): \"{$v}\"");
        }
    }
}
