<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования (JSONL, одно событие на строку).
 *
 * Идемпотентность: дедупликация по естественному ключу (provider, provider_request_id).
 * Повторная доставка уже known-события целиком игнорируется. Одинаковый
 * provider_request_id у разных провайдеров — разные события.
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Возвращает массив результата: сколько событий добавлено и
     * список ошибок по строкам (частичная загрузка — валидные записываются,
     * невалидные отчитываются).
     *
     * @return array{inserted: int, errors: string[]}
     */
    public function import(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл событий не найден или недоступен: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException('Не удалось прочитать файл событий');
        }

        // Разбор и валидация каждой строки; ошибки по строкам не отменяют
        // валидные — это «частичная ошибка». Читаем их в список.
        $lines = preg_split('/\R/', (string) $raw);
        $events = [];
        $errors = [];
        foreach ($lines as $n => $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            try {
                $events[] = $this->parseLine($n + 1, $line);
            } catch (ImportException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($events === []) {
            return ['inserted' => 0, 'errors' => $errors];
        }

        // Ключи, уже присутствующие в БД (дедупликация).
        $existing = $this->existingKeys();

        // Дедупликация внутри батча.
        $new = [];
        $seen = [];
        foreach ($events as $event) {
            $key = $event['provider'] . "\0" . $event['provider_request_id'];
            if (isset($seen[$key]) || isset($existing[$key])) {
                continue;
            }
            $seen[$key] = true;
            $new[] = $event;
        }

        $inserted = 0;
        if ($new !== []) {
            $this->em->wrapInTransaction(function () use ($new, &$inserted): void {
                foreach ($new as $e) {
                    $this->em->persist(new UsageEvent(
                        $e['provider'],
                        $e['provider_request_id'],
                        $e['model'],
                        $e['user'],
                        $e['ts'],
                        $e['tokens_input'],
                        $e['tokens_output'],
                        $e['tokens_cache_read'],
                        $e['tokens_cache_write'],
                    ));
                }
                $this->em->flush();
                $inserted = \count($new);
            });
        }

        return ['inserted' => $inserted, 'errors' => $errors];
    }

    public function parseLine(int $lineNo, string $line): array
    {
        try {
            $data = json_decode($line, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('Строка %d: некорректный JSON (%s)', $lineNo, $e->getMessage()));
        }
        if (!is_array($data)) {
            throw new ImportException(sprintf('Строка %d: событие должно быть объектом JSON', $lineNo));
        }

        $provider = $this->strField($data, 'provider', $lineNo);
        $providerRequestId = $this->strField($data, 'provider_request_id', $lineNo);
        $model = $this->strField($data, 'model', $lineNo);
        $user = $this->strField($data, 'user', $lineNo);

        $tsRaw = $data['ts'] ?? null;
        if (!is_string($tsRaw)) {
            throw new ImportException(sprintf('Строка %d: ts должен быть ISO-8601 (строкой)', $lineNo));
        }
        $ts = self::parseTs($lineNo, $tsRaw);

        $tokensInput = $this->nonNegativeInt($data, 'tokens_input', $lineNo);
        $tokensOutput = $this->nonNegativeInt($data, 'tokens_output', $lineNo);
        $tokensCacheRead = $this->nonNegativeInt($data, 'tokens_cache_read', $lineNo);
        $tokensCacheWrite = $this->nonNegativeInt($data, 'tokens_cache_write', $lineNo);

        return [
            'provider' => $provider,
            'provider_request_id' => $providerRequestId,
            'model' => $model,
            'user' => $user,
            'ts' => $ts,
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'tokens_cache_read' => $tokensCacheRead,
            'tokens_cache_write' => $tokensCacheWrite,
        ];
    }

    /** @return array<string,bool> ключ (provider \0 provider_request_id) -> true */
    private function existingKeys(): array
    {
        $conn = $this->em->getConnection();
        $result = [];
        $rows = $conn->fetchAllAssociative('SELECT provider, provider_request_id FROM usage_events');
        foreach ($rows as $row) {
            $result[$row['provider'] . "\0" . $row['provider_request_id']] = true;
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function strField(array $data, string $field, int $lineNo): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('Строка %d: поле "%s" должно быть непустой строкой', $lineNo, $field));
        }

        return $value;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function nonNegativeInt(array $data, string $field, int $lineNo): int
    {
        $value = $data[$field] ?? null;
        if (is_int($value)) {
            if ($value < 0) {
                throw new ImportException(sprintf('Строка %d: поле "%s" должно быть целым >= 0', $lineNo, $field));
            }
            return $value;
        }
        if (is_string($value) && preg_match('/^\d+$/', $value)) {
            return (int) $value;
        }

        throw new ImportException(sprintf('Строка %d: поле "%s" должно быть целым >= 0', $lineNo, $field));
    }

    private static function parseTs(int $lineNo, string $iso): \DateTimeImmutable
    {
        $trimmed = trim($iso);
        if ($trimmed === '') {
            throw new ImportException(sprintf('Строка %d: пустой ts', $lineNo));
        }
        try {
            $dt = new \DateTimeImmutable($trimmed, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException(sprintf('Строка %d: некорректный ts "%s"', $lineNo, $iso));
        }

        return $dt->setTimezone(new \DateTimeZone('UTC'));
    }
}
