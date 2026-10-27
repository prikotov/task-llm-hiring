<?php

declare(strict_types=1);

namespace App\Infra\Input;

use App\Domain\UsageEvent;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Разбор JSONL-событий использования.
 *
 * @param array<int, array<string, mixed>> $lines одна запись на строку
 * @return UsageEvent[]
 */
final class UsageEventParser
{
    public static function parse(array $lines): array
    {
        $result = [];
        foreach ($lines as $i => $r) {
            if (!is_array($r)) {
                throw new InvalidArgumentException("usage row {$i}: JSON object expected");
            }

            $provider = self::reqString($r, 'provider', $i);
            $providerRequestId = self::reqString($r, 'provider_request_id', $i);
            $model = self::reqString($r, 'model', $i);
            $user = self::reqString($r, 'user', $i);
            $ts = self::reqTs($r, $i);

            $tokens = [
                'input' => self::reqNonNegInt((array) $r, 'tokens_input', $i),
                'output' => self::reqNonNegInt((array) $r, 'tokens_output', $i),
                'cache_read' => self::reqNonNegInt((array) $r, 'tokens_cache_read', $i),
                'cache_write' => self::reqNonNegInt((array) $r, 'tokens_cache_write', $i),
            ];

            $result[] = new UsageEvent($provider, $providerRequestId, $model, $user, $ts, $tokens);
        }

        return $result;
    }

    private static function reqString(array $r, string $key, int $i): string
    {
        $v = $r[$key] ?? null;
        if (!is_string($v) || $v === '') {
            throw new InvalidArgumentException("usage row {$i}: {$key} — non-empty string required");
        }

        return $v;
    }

    private static function reqTs(array $r, int $i): DateTimeImmutable
    {
        $v = $r['ts'] ?? null;
        if (!is_string($v)) {
            throw new InvalidArgumentException("usage row {$i}: ts — ISO8601 string required");
        }
        $v = trim($v);
        $ts = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $v, new DateTimeZone('UTC'));
        if ($ts === false) {
            $ts = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $v, new DateTimeZone('UTC'));
        }
        if ($ts === false) {
            $ts = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', $v, new DateTimeZone('UTC'));
        }
        if ($ts === false) {
            throw new InvalidArgumentException("usage row {$i}: ts 'YYYY-MM-DDTHH:MM:SSZ' required (got '{$v}')");
        }

        return $ts;
    }

    private static function reqNonNegInt(array $r, string $key, int $i): string
    {
        $v = $r[$key] ?? null;
        if (is_int($v)) {
            if ($v < 0) {
                throw new InvalidArgumentException("usage row {$i}: {$key} — negative not allowed");
            }
            return (string) $v;
        }
        if (is_string($v) && preg_match('/^\d+$/', $v)) {
            return $v;
        }
        throw new InvalidArgumentException("usage row {$i}: {$key} — non-negative integer required");
    }
}
