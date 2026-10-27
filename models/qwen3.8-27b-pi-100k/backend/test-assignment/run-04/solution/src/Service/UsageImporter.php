<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём журнала событий использования (JSONL).
 *
 * Идемпотентность: дедупликация по (provider, provider_request_id) —
 * повторная доставка уже известного события полностью игнорируется
 * (INSERT ... ON CONFLICT DO NOTHING).
 *
 * Атомарность файла: сначала валидируются все строки; если хотя бы одна
 * строка некорректна — ошибка и ни одна валидная строка не загружается.
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @psalm-return list<array{provider: string, provider_request_id: string, model: string, user: string, ts: \DateTimeImmutable, tokens: list<int>}>
     *
     * @return array{imported: int, duplicates: int}
     *
     * @throws ImportException при некорректной строке JSONL или данных
     */
    public function import(string $file): array
    {
        $events = [];

        $handle = \fopen($file, 'r');
        if ($handle === false) {
            throw new ImportException(sprintf('не удалось открыть файл: %s', $file));
        }

        $lineNumber = 0;
        while (($line = \fgets($handle)) !== false) {
            $lineNumber++;
            if (trim($line) === '') {
                continue; // пустые строки пропускаем
            }

            $raw = \json_decode($line, true);
            if (!\is_array($raw)) {
                throw new ImportException(sprintf('строка %d: некорректный JSON', $lineNumber));
            }

            $events[] = $this->validateLine($raw, $lineNumber);
        }
        \fclose($handle);

        return $this->insertAll($events);
    }

    /**
     * @psalm-param array<string, mixed> $raw
     * @return array{provider: string, provider_request_id: string, model: string, user: string, ts: \DateTimeImmutable, tokens: list<int>}
     */
    private function validateLine(array $raw, int $lineNumber): array
    {
        $string = static function (mixed $value, string $field) use ($lineNumber): string {
            if (!\is_string($value) || $value === '') {
                throw new ImportException(sprintf('строка %d: поле "%s" обязательно и должно быть непустой строкой', $lineNumber, $field));
            }

            return $value;
        };

        $provider = $string($raw['provider'] ?? null, 'provider');
        $requestId = $string($raw['provider_request_id'] ?? null, 'provider_request_id');
        $model = $string($raw['model'] ?? null, 'model');
        $user = $string($raw['user'] ?? null, 'user');

        $tsRaw = $string($raw['ts'] ?? null, 'ts');
        $ts = self::parseUtc($tsRaw, $lineNumber);

        $tokens = [];
        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $i => $field) {
            $tokens[$i] = self::parseTokenCount($raw[$field] ?? 0, $field, $lineNumber);
        }

        return [
            'provider' => $provider,
            'provider_request_id' => $requestId,
            'model' => $model,
            'user' => $user,
            'ts' => $ts,
            'tokens' => $tokens,
        ];
    }

    /**
     * @psalm-param list<array{provider: string, provider_request_id: string, model: string, user: string, ts: \DateTimeImmutable, tokens: list<int>}> $events
     *
     * @return array{imported: int, duplicates: int}
     */
    private function insertAll(array $events): array
    {
        if ($events === []) {
            return ['imported' => 0, 'duplicates' => 0];
        }

        $connection = $this->em->getConnection();
        $imported = 0;

        $connection->transactional(static function () use ($connection, $events, &$imported): void {
            foreach ($events as $e) {
                $inserted = $connection->executeStatement(
                    'INSERT INTO usage_events
                        (provider, provider_request_id, model, user_ref, ts,
                         tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
                         status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                     ON CONFLICT (provider, provider_request_id) DO NOTHING',
                    [
                        $e['provider'],
                        $e['provider_request_id'],
                        $e['model'],
                        $e['user'],
                        $e['ts']->format('Y-m-d H:i:s'),
                        $e['tokens'][0],
                        $e['tokens'][1],
                        $e['tokens'][2],
                        $e['tokens'][3],
                        \App\Entity\UsageEvent::STATUS_PENDING,
                    ],
                );

                $imported += $inserted;
            }
        });

        return ['imported' => $imported, 'duplicates' => count($events) - $imported];
    }

    /**
     * @param mixed $value
     *
     * @throws ImportException
     */
    public static function parseUtc(string $value, int $lineNumber): \DateTimeImmutable
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,9})?Z$/', $value)) {
            throw new ImportException(sprintf(
                'строка %d: ts ожидается в ISO-8601, UTC (например 2026-09-15T11:00:00Z), получено "%s"',
                $lineNumber,
                $value,
            ));
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException(sprintf('строка %d: ts не приводится к дате ("%s")', $lineNumber, $value));
        }
    }

    /**
     * @param mixed $value
     *
     * @throws ImportException
     */
    private static function parseTokenCount($value, string $field, int $lineNumber): int
    {
        if (\is_int($value)) {
            $n = $value;
        } elseif (\is_string($value) && preg_match('/^\d+$/', $value)) {
            $n = (int) $value;
        } else {
            throw new ImportException(sprintf('строка %d: поле "%s" должно быть целым числом ≥ 0', $lineNumber, $field));
        }

        if ($n < 0) {
            throw new ImportException(sprintf('строка %d: поле "%s" не может быть отрицательным', $lineNumber, $field));
        }

        if ($n > 9_000_000_000_000_000_000) {
            throw new ImportException(sprintf('строка %d: поле "%s" превышает пределы целочисленного типа', $lineNumber, $field));
        }

        return $n;
    }
}
