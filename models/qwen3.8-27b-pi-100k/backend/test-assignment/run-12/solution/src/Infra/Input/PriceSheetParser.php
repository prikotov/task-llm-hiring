<?php

declare(strict_types=1);

namespace App\Infra\Input;

use App\Domain\PriceSheet;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Разбор секции `prices` из prices.json.
 *
 * @param array<int, array<string, mixed>> $prices
 * @return PriceSheet[]
 */
final class PriceSheetParser
{
    public static function parse(array $prices): array
    {
        if (!is_array($prices)) {
            throw new InvalidArgumentException('prices: array expected');
        }

        $result = [];
        foreach ($prices as $p) {
            $result[] = self::one($p);
        }

        return $result;
    }

    /** @param array<string, mixed> $p */
    private static function one(array $p): PriceSheet
    {
        $where = 'prices[].*';
        $provider = self::reqString($p, 'provider', $where);
        $model = self::reqString($p, 'model', $where);
        $validFrom = self::reqTs($p, 'valid_from', $where, $provider, $model);

        $unit = $p['unit'] ?? null;
        if (!is_string($unit) || !in_array($unit, [PriceSheet::UNIT_PER_TOKEN, PriceSheet::UNIT_PER_TOKEN_UNIFIED], true)) {
            throw new InvalidArgumentException("prices[].unit: per_1m_tokens | per_1m_tokens_unified");
        }

        $rates = $p['rates'] ?? null;
        if (!is_array($rates)) {
            throw new InvalidArgumentException("prices.{$provider}.{$model}.rates: object expected");
        }

        $norm = [];
        if ($unit === PriceSheet::UNIT_PER_TOKEN) {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $k) {
                if (!array_key_exists($k, $rates)) {
                    throw new InvalidArgumentException("prices.{$provider}.{$model}.rates.{$k}: missing");
                }
                $norm[$k] = self::canonical((string) self::reqDecimal($rates[$k], "prices.{$provider}.{$model}.rates.{$k}"));
            }
        } else {
            if (!array_key_exists('any', $rates)) {
                throw new InvalidArgumentException("prices.{$provider}.{$model}.rates.any: missing");
            }
            $norm['any'] = self::canonical((string) self::reqDecimal($rates['any'], "prices.{$provider}.{$model}.rates.any"));
        }

        return new PriceSheet($provider, $model, $validFrom, $unit, $norm);
    }

    private static function reqString(array $r, string $key, string $where): string
    {
        $v = $r[$key] ?? null;
        if (!is_string($v) || $v === '') {
            throw new InvalidArgumentException("{$where}.{$key}: non-empty string required");
        }

        return $v;
    }

    /** @param array<string, mixed> $p */
    private static function reqTs(array $p, string $key, string $where, string $provider, string $model): DateTimeImmutable
    {
        $v = $p[$key] ?? null;
        if (!is_string($v)) {
            throw new InvalidArgumentException("prices.{$provider}.{$model}.{$key}: ISO8601 string required");
        }
        $v = trim($v);
        $ts = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $v, new DateTimeZone('UTC'));
        if ($ts === false) {
            // допускаем микросекунды и +00:00
            $ts = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $v, new DateTimeZone('UTC'));
        }
        if ($ts === false) {
            $ts = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', $v, new DateTimeZone('UTC'));
        }
        if ($ts === false) {
            throw new InvalidArgumentException("prices.{$provider}.{$model}.{$key}: 'YYYY-MM-DDTHH:MM:SSZ' required (got '{$v}')");
        }

        return $ts;
    }

    private static function reqDecimal(mixed $v, string $what): string
    {
        if (is_int($v)) {
            return (string) $v;
        }
        if (is_float($v)) {
            throw new InvalidArgumentException("{$what}: JSON number not allowed — use string");
        }
        if (!is_string($v) || !preg_match('/^(0|[1-9]\d*)(\.\d+)?$/', $v)) {
            throw new InvalidArgumentException("{$what}: non-negative decimal string required");
        }

        return $v;
    }

    private static function canonical(string $s): string
    {
        if (bccomp($s, '0', 40) === 0) {
            return '0';
        }
        $s = ltrim($s, '0');
        if ($s === '') {
            return '0';
        }
        if (str_ends_with($s, '.')) {
            $s = substr($s, 0, -1);
        }
        if (!str_contains($s, '.')) {
            return $s;
        }
        $s = rtrim($s, '0');
        if (str_ends_with($s, '.')) {
            $s = substr($s, 0, -1);
        }

        return $s;
    }
}
