<?php

declare(strict_types=1);

namespace App\Infra\Input;

use App\Domain\OffPeak;
use App\Domain\Provider;
use InvalidArgumentException;

/**
 * Разбор секции `providers` из prices.json.
 *
 * @param array<int, array<string, mixed>> $providers
 * @return array<string, Provider> ключ — код провайдера
 */
final class CatalogParser
{
    public static function parseProviders(array $providers): array
    {
        if (!is_array($providers)) {
            throw new InvalidArgumentException('providers: array expected');
        }

        $result = [];
        foreach ($providers as $p) {
            if (!is_array($p)) {
                throw new InvalidArgumentException('providers[].*: object expected');
            }
            $code = self::reqString($p, 'code', 'providers');
            $name = self::reqString($p, 'name', 'providers');

            $offPeak = null;
            if (isset($p['off_peak'])) {
                if ($p['off_peak'] === null) {
                    $offPeak = null;
                } elseif (is_array($p['off_peak'])) {
                    $offPeak = OffPeak::fromConfig($p['off_peak']);
                } else {
                    throw new InvalidArgumentException('providers[].off_peak: object or null');
                }
            }

            $result[$code] = new Provider($code, $name, $offPeak);
        }

        return $result;
    }

    private static function reqString(array $r, string $key, string $where): string
    {
        $v = $r[$key] ?? null;
        if (!is_string($v) || $v === '') {
            throw new InvalidArgumentException("{$where}[].{$key}: non-empty string required");
        }

        return $v;
    }
}
