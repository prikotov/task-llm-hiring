<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Application\Exception\InputException;
use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Usage\TokenUsage;
use App\Billing\Domain\Usage\UsageEvent;

/**
 * Разбор JSONL-файла событий использования.
 *
 * Пустые строки пропускаются (типичный хвост файла). Любая некорректная
 * строка — InputException с номером строки: «Некорректная строка JSONL —
 * ошибка, код 1».
 */
final class UsageFileParser
{
    /**
     * @return list<UsageEvent>
     */
    public function parse(string $path): array
    {
        $handle = @fopen($path, 'rb');

        if (false === $handle) {
            throw new InputException(sprintf('Не удалось открыть файл "%s"', $path));
        }

        $events = [];
        $lineNumber = 0;

        try {
            while (false !== ($line = fgets($handle))) {
                ++$lineNumber;

                $line = rtrim($line, "\r\n");

                if ('' === trim($line)) {
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
        $where = sprintf('строка %d', $lineNumber);

        try {
            $item = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputException(sprintf('%s: некорректный JSON (%s)', $where, $e->getMessage()), previous: $e);
        }

        if (!is_array($item)) {
            throw new InputException($where.': ожидался JSON-объект');
        }

        try {
            $model = new ModelReference(
                $this->requireString($item, 'provider', $where),
                $this->requireString($item, 'model', $where),
            );

            return new UsageEvent(
                null,
                $this->requireString($item, 'provider_request_id', $where),
                $model,
                $this->requireString($item, 'user', $where),
                $this->parseTimestamp($this->requireString($item, 'ts', $where), $where.'.ts'),
                TokenUsage::of(
                    $this->requireInt($item, 'tokens_input', $where),
                    $this->requireInt($item, 'tokens_output', $where),
                    $this->requireInt($item, 'tokens_cache_read', $where),
                    $this->requireInt($item, 'tokens_cache_write', $where),
                ),
            );
        } catch (\InvalidArgumentException $e) {
            throw new InputException(sprintf('%s: %s', $where, $e->getMessage()), previous: $e);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function requireString(array $item, string $key, string $where): string
    {
        if (!isset($item[$key]) || !is_string($item[$key]) || '' === trim($item[$key])) {
            throw new InputException(sprintf('%s.%s: обязательная непустая строка отсутствует', $where, $key));
        }

        return $item[$key];
    }

    /**
     * @param array<string, mixed> $item
     */
    private function requireInt(array $item, string $key, string $where): int
    {
        if (!array_key_exists($key, $item) || !is_int($item[$key])) {
            throw new InputException(sprintf('%s.%s: ожидалось целое число', $where, $key));
        }

        return $item[$key];
    }

    private function parseTimestamp(string $value, string $where): \DateTimeImmutable
    {
        if (1 !== preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value)) {
            throw new InputException(sprintf('%s: ожидался момент UTC вида 2026-09-15T11:00:00Z, получено "%s"', $where, $value));
        }

        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z', $value, new \DateTimeZone('UTC'));

        if (false === $parsed || $parsed->format('Y-m-d\TH:i:s\Z') !== $value) {
            throw new InputException(sprintf('%s: некорректная дата "%s"', $where, $value));
        }

        return $parsed;
    }
}
