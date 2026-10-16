<?php

declare(strict_types=1);

namespace App\Ingest;

use App\Domain\DomainException;

/**
 * Разбор одной строки JSONL журнала использования.
 */
final class UsageLineParser
{
    /**
     * @throws DomainException
     */
    public function parse(array|string $line, int $lineNumber): UsageEventInput
    {
        $where = 'строка '.$lineNumber;

        if (\is_string($line)) {
            try {
                $line = json_decode($line, true, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new DomainException(sprintf('%s: некорректный JSON: %s', $where, $e->getMessage()), 0, $e);
            }
        }
        if (!\is_array($line)) {
            throw new DomainException(sprintf('%s: ожидается JSON-объект', $where));
        }

        $provider = $this->stringField($line, 'provider', $where);
        $providerRequestId = $this->stringField($line, 'provider_request_id', $where);
        $model = $this->stringField($line, 'model', $where);
        $user = $this->stringField($line, 'user', $where);
        $ts = $this->parseUtc($line['ts'] ?? null, $where);

        return new UsageEventInput(
            provider: $provider,
            providerRequestId: $providerRequestId,
            model: $model,
            user: $user,
            occurredAt: $ts,
            tokensInput: $this->tokenField($line, 'tokens_input', $where),
            tokensOutput: $this->tokenField($line, 'tokens_output', $where),
            tokensCacheRead: $this->tokenField($line, 'tokens_cache_read', $where),
            tokensCacheWrite: $this->tokenField($line, 'tokens_cache_write', $where),
        );
    }

    private function stringField(array $line, string $field, string $where): string
    {
        $value = $line[$field] ?? null;
        if (!\is_string($value) || $value === '') {
            throw new DomainException(sprintf('%s: поле "%s" должно быть непустой строкой', $where, $field));
        }

        return $value;
    }

    private function tokenField(array $line, string $field, string $where): int
    {
        $value = $line[$field] ?? null;
        if (\is_int($value)) {
            return $value >= 0 ? $value : $this->invalid($where, $field, 'целым числом >= 0');
        }
        if (\is_string($value) && preg_match('/^\d+$/', $value)) {
            return (int) $value;
        }

        return $this->invalid($where, $field, 'целым числом >= 0');
    }

    /**
     * @never
     */
    private function invalid(string $where, string $field, string $what): int
    {
        throw new DomainException(sprintf('%s: поле "%s" должно быть %s', $where, $field, $what));
    }

    private function parseUtc(mixed $value, string $where): \DateTimeImmutable
    {
        if (!\is_string($value) || !preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|[+-]\d{2}:?\d{2})$/', $value)) {
            throw new DomainException(sprintf('%s: "ts" — ожидается ISO-8601 со смещением (UTC)', $where));
        }

        try {
            return (new \DateTimeImmutable($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new DomainException(sprintf('%s: "ts" — некорректная дата "%s"', $where, $value), 0, $e);
        }
    }
}
