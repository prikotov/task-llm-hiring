<?php

declare(strict_types=1);

namespace App\Infrastructure\Json;

use App\Domain\Usage\TokenUsage;
use App\Domain\Usage\UsageEvent;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use RuntimeException;

/**
 * Читает JSONL-файл событий использования. Строки разбираются по одной,
 * чтобы ошибка указывала на конкретную строку файла.
 */
final class UsageEventReader
{
    /**
     * @return list<UsageEvent>
     */
    public function read(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException(sprintf('Файл не найден или недоступен для чтения: %s', $path));
        }

        $handle = fopen($path, 'rb');
        if (false === $handle) {
            throw new RuntimeException(sprintf('Не удалось открыть файл: %s', $path));
        }

        $events = [];
        $lineNumber = 0;

        try {
            while (false !== $line = fgets($handle)) {
                ++$lineNumber;
                $line = trim($line);
                if ('' === $line) {
                    continue;
                }

                $events[] = $this->parseLine($line, $lineNumber);
            }
        } finally {
            fclose($handle);
        }

        return $events;
    }

    private function parseLine(string $line, int $lineNumber): UsageEvent
    {
        try {
            /** @var mixed $decoded */
            $decoded = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException(sprintf('Строка %d: некорректный JSON — %s', $lineNumber, $e->getMessage()), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new InvalidArgumentException(sprintf('Строка %d: ожидался JSON-объект.', $lineNumber));
        }

        try {
            return new UsageEvent(
                providerCode: $this->stringField($decoded, 'provider', $lineNumber),
                providerRequestId: $this->stringField($decoded, 'provider_request_id', $lineNumber),
                modelCode: $this->stringField($decoded, 'model', $lineNumber),
                userRef: $this->stringField($decoded, 'user', $lineNumber),
                occurredAt: self::parseTimestamp($this->stringField($decoded, 'ts', $lineNumber)),
                tokens: new TokenUsage(
                    $this->intField($decoded, 'tokens_input', $lineNumber),
                    $this->intField($decoded, 'tokens_output', $lineNumber),
                    $this->intField($decoded, 'tokens_cache_read', $lineNumber),
                    $this->intField($decoded, 'tokens_cache_write', $lineNumber),
                ),
            );
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf('Строка %d: %s', $lineNumber, $e->getMessage()), 0, $e);
        }
    }

    /**
     * Строгий разбор ISO-8601 UTC: YYYY-MM-DDTHH:MM:SS[.ffffff]Z.
     * Допускается и смещение "+00:00", другие зоны — ошибка.
     */
    public static function parseTimestamp(string $value): DateTimeImmutable
    {
        if (1 !== preg_match('/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(\.\d{1,6})?Z$/', $value, $m)) {
            throw new InvalidArgumentException(sprintf('Ожидался ISO-8601 UTC (…Z), получено "%s".', $value));
        }

        $candidate = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $m[1] . 'T' . $m[2],
            new DateTimeZone('UTC'),
        );

        if (false === $candidate) {
            throw new InvalidArgumentException(sprintf('Некорректная дата/время "%s".', $value));
        }

        // createFromFormat «залипает» на переполнениях (32 января -> 1 февраля),
        // поэтому сверяем обратное форматирование.
        if ($candidate->format('Y-m-d\TH:i:s') !== $m[1] . 'T' . $m[2]) {
            throw new InvalidArgumentException(sprintf('Некорректная дата/время "%s".', $value));
        }

        return $candidate;
    }

    /**
     * @param array<mixed> $data
     */
    private function stringField(array $data, string $field, int $lineNumber): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || '' === $value) {
            throw new InvalidArgumentException(sprintf('поле "%s" должно быть непустой строкой.', $field));
        }

        return $value;
    }

    /**
     * @param array<mixed> $data
     */
    private function intField(array $data, string $field, int $lineNumber): int
    {
        $value = $data[$field] ?? 0;
        if (is_float($value) && floor($value) === $value) {
            $value = (int) $value;
        }
        if (!is_int($value) || $value < 0) {
            throw new InvalidArgumentException(sprintf('поле "%s" должно быть целым >= 0.', $field));
        }

        return $value;
    }
}
