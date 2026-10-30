<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\EventStatus;
use App\Util\DateTimeUtc;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;

/**
 * Импорт событий использования из JSONL.
 *
 * Правила приёма:
 * - файл проверяется целиком; некорректная строка ⇒ ошибка, в базу ничего не записывается;
 * - дедупликация по (provider, provider_request_id): повторная доставка
 *   игнорируется целиком (INSERT ... ON CONFLICT DO NOTHING, обновления нет);
 * - одинаковый provider_request_id у разных провайдеров — разные события.
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{imported: int, duplicates: int}
     *
     * @throws ImportException
     * @throws \DomainException
     */
    public function importFile(string $path, int $batchSize = 500): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл не найден или недоступен: "%s".', $path));
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new ImportException(sprintf('Не удалось открыть файл: "%s".', $path));
        }

        $lines = [];
        try {
            $lineNo = 0;
            while (($line = fgets($handle)) !== false) {
                $lineNo++;
                $trimmed = trim($line);
                if ($trimmed === '') {
                    continue; // пустые строки (в т.ч. в конце файла) не считаются событиями
                }
                $lines[] = [$this->parseLine($trimmed, $lineNo), $lineNo];
            }
        } finally {
            fclose($handle);
        }

        if ($lines === []) {
            return ['imported' => 0, 'duplicates' => 0];
        }

        $con = $this->em->getConnection();

        $providers = [];
        foreach ($con->fetchAllAssociative('SELECT id, code FROM providers') as $row) {
            $providers[(string) $row['code']] = (int) $row['id'];
        }

        /** @var array<string, array{modelId: int, providerId: int}> $models */
        $models = [];
        foreach (
            $con->fetchAllAssociative(
                'SELECT m.id AS id, p.code AS provider_code, m.code AS model_code
                 FROM models m
                 JOIN providers p ON p.id = m.provider_id',
            ) as $row
        ) {
            $models[(string) $row['provider_code'] . '|' . (string) $row['model_code']] = [
                'modelId' => (int) $row['id'],
                'providerId' => (int) $providers[(string) $row['provider_code']],
            ];
        }

        $inserted = $con->transactional(function () use ($con, $lines, $providers, $models, $batchSize): int {
            $total = 0;

            foreach (array_chunk($lines, $batchSize) as $chunk) {
                $placeholders = [];
                $params = [];
                $types = [];

                foreach ($chunk as [$event, $lineNo]) {
                    $providerId = $providers[$event['provider']] ?? null;
                    if ($providerId === null) {
                        throw new ImportException(sprintf('Строка %d: провайдер "%s" не найден в справочнике цен; сначала выполните app:prices:import.', $lineNo, $event['provider']));
                    }

                    $model = $models[$event['provider'] . '|' . $event['model']] ?? null;
                    if ($model === null) {
                        throw new ImportException(sprintf('Строка %d: модель "%s/%s" не найдена в справочнике цен; сначала выполните app:prices:import.', $lineNo, $event['provider'], $event['model']));
                    }

                    $placeholders[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                    $params[] = $providerId;
                    $params[] = $event['provider_request_id'];
                    $params[] = $model['modelId'];
                    $params[] = $event['user'];
                    $params[] = $event['ts'];
                    $params[] = $event['tokens_input'];
                    $params[] = $event['tokens_output'];
                    $params[] = $event['tokens_cache_read'];
                    $params[] = $event['tokens_cache_write'];
                    $params[] = EventStatus::Pending->value;
                    $params[] = $event['now'];

                    $types[] = Types::INTEGER;
                    $types[] = 'string';
                    $types[] = Types::INTEGER;
                    $types[] = 'string';
                    $types[] = Types::DATETIMETZ_IMMUTABLE;
                    $types[] = Types::INTEGER;
                    $types[] = Types::INTEGER;
                    $types[] = Types::INTEGER;
                    $types[] = Types::INTEGER;
                    $types[] = 'string';
                    $types[] = Types::DATETIMETZ_IMMUTABLE;
                }

                $sql = 'INSERT INTO usage_events '
                    . '(provider_id, provider_request_id, model_id, "user", ts, '
                    . 'tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, status, created_at) '
                    . 'VALUES ' . implode(', ', $placeholders) . ' '
                    . 'ON CONFLICT (provider_id, provider_request_id) DO NOTHING';

                $total += (int) $con->executeStatement($sql, $params, $types);
            }

            return $total;
        });

        return ['imported' => $inserted, 'duplicates' => count($lines) - $inserted];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ImportException
     * @throws \DomainException
     */
    private function parseLine(string $line, int $lineNo): array
    {
        /** @var mixed $data */
        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ImportException(sprintf('Строка %d: некорректный JSON (%s).', $lineNo, $e->getMessage()), 0, $e);
        }

        if (!is_array($data)) {
            throw new ImportException(sprintf('Строка %d: ожидался JSON-объект.', $lineNo));
        }

        $ts = self::dateTimeOf($data, $lineNo);

        $tokens = [];
        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $field) {
            $value = $data[$field] ?? null;
            if (!is_int($value) || $value < 0) {
                throw new ImportException(sprintf('Строка %d: поле "%s" должно быть целым числом >= 0.', $lineNo, $field));
            }
            $tokens[$field] = $value;
        }

        return [
            'provider' => self::stringOf($data, 'provider', $lineNo),
            'provider_request_id' => self::stringOf($data, 'provider_request_id', $lineNo),
            'model' => self::stringOf($data, 'model', $lineNo),
            'user' => self::stringOf($data, 'user', $lineNo),
            'ts' => $ts,
            'tokens_input' => $tokens['tokens_input'],
            'tokens_output' => $tokens['tokens_output'],
            'tokens_cache_read' => $tokens['tokens_cache_read'],
            'tokens_cache_write' => $tokens['tokens_cache_write'],
            'now' => DateTimeUtc::now(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws ImportException
     */
    private static function stringOf(array $data, string $key, int $lineNo): string
    {
        $value = $data[$key] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('Строка %d: поле "%s" отсутствует либо не непустая строка.', $lineNo, $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws ImportException
     * @throws \DomainException
     */
    private static function dateTimeOf(array $data, int $lineNo): \DateTimeImmutable
    {
        $value = $data['ts'] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('Строка %d: поле "ts" отсутствует либо не строка (ISO-8601, UTC).', $lineNo));
        }

        return DateTimeUtc::parse($value);
    }
}
