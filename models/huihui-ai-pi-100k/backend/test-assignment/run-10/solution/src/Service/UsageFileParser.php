<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\TokenCounts;
use App\Dto\UsageEventData;
use App\Exception\InputError;

/**
 * Разбирает и валидирует JSONL-файл с событиями.
 * Одна строка — одно событие; пустые строки допустимы и пропускаются.
 *
 * @return UsageEventData[]
 */
final class UsageFileParser
{
    /**
     * @return UsageEventData[]
     */
    public function parse(string $path): array
    {
        if (!is_file($path)) {
            throw InputError::because(string: 'Файл событий не найден: ' . $path);
        }

        $lines = file($path, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = $lines === null || $lines === false ? [] : array_map(static fn (string $l): string => trim($l), $lines);
        $lines = array_values(array_filter($lines, static fn (string $l): bool => $l !== ''));

        $out = [];
        foreach ($lines as $i => $line) {
            $where = sprintf('events.jsonl line %d', $i + 1);
            $out[] = $this->parseLine($line, $where);
        }

        return $out;
    }

    private function parseLine(string $line, string $where): UsageEventData
    {
        $raw = json_decode($line, true, 512, flags: JSON_THROW_ON_ERROR);
        if (!is_array($raw)) {
            throw InputError::because($where . ': событие — объект JSON');
        }

        $provider = Parsing::requireString($raw, 'provider', $where);
        $providerRequestId = Parsing::requireString($raw, 'provider_request_id', $where);
        $model = Parsing::requireString($raw, 'model', $where);
        $user = Parsing::requireString($raw, 'user', $where);
        $ts = Parsing::iso8601Utc(Parsing::requireString($raw, 'ts', $where));

        $counts = new TokenCounts(
            input: Parsing::requireNonNegativeInt($raw, 'tokens_input', $where),
            output: Parsing::requireNonNegativeInt($raw, 'tokens_output', $where),
            cacheRead: Parsing::requireNonNegativeInt($raw, 'tokens_cache_read', $where),
            cacheWrite: Parsing::requireNonNegativeInt($raw, 'tokens_cache_write', $where),
        );

        return new UsageEventData($provider, $providerRequestId, $model, $user, $ts, $counts);
    }
}
