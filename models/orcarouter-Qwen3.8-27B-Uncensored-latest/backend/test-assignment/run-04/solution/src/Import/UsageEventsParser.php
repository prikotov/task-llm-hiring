<?php

declare(strict_types=1);

namespace App\Import;

use App\Shared\ImportException;

/**
 * Decodes and validates one JSONL line of a usage events file.
 */
final class UsageEventsParser
{
    private const TOKEN_FIELDS = [
        'tokens_input',
        'tokens_output',
        'tokens_cache_read',
        'tokens_cache_write',
    ];

    public function parseLine(string $json, int $lineNo): UsageEventData
    {
        $decoded = \json_decode($json, true);

        if (\json_last_error() !== \JSON_ERROR_NONE || !\is_array($decoded)) {
            throw ImportException::forLine($lineNo, \sprintf('invalid JSON: %s', \json_last_error_msg()));
        }

        try {
            $provider = $this->word($decoded['provider'] ?? null, 'provider');
            $requestId = $this->word($decoded['provider_request_id'] ?? null, 'provider_request_id');
            $model = $this->word($decoded['model'] ?? null, 'model');
            $user = $this->word($decoded['user'] ?? null, 'user');
            $ts = $this->isoUtc($decoded['ts'] ?? null, 'ts');

            $tokens = [];
            foreach (self::TOKEN_FIELDS as $field) {
                $tokens[$field] = $this->nonNegativeInt($decoded[$field] ?? null, $field);
            }

            return new UsageEventData(
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
        } catch (ImportException $e) {
            throw ImportException::forLine($lineNo, $e->getMessage());
        }
    }

    private function word(mixed $value, string $field): string
    {
        if (!\is_string($value) || $value === '' || \strlen($value) > 128) {
            throw new ImportException(\sprintf('"%s" must be a non-empty string (<= 128 chars)', $field));
        }

        return $value;
    }

    private function nonNegativeInt(mixed $value, string $field): int
    {
        if (\is_bool($value) || (!\is_int($value) && !(\is_string($value) && \preg_match('/^\d+$/', $value) === 1)) || ($value < 0)) {
            throw new ImportException(\sprintf('"%s" must be an integer >= 0', $field));
        }

        $int = (int) $value;

        if ($int > (int) \PHP_INT_MAX) {
            throw new ImportException(\sprintf('"%s" is too large', $field));
        }

        return $int;
    }

    private function isoUtc(mixed $value, string $field): \DateTimeImmutable
    {
        if (!\is_string($value) || \preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/', $value) !== 1) {
            throw new ImportException(\sprintf('"%s" must be ISO-8601 UTC, e.g. "2026-09-15T11:00:00Z"', $field));
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value);
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);
        }

        if ($dt === false) {
            throw new ImportException(\sprintf('"%s" is not a valid date-time', $field));
        }

        return $dt;
    }
}
