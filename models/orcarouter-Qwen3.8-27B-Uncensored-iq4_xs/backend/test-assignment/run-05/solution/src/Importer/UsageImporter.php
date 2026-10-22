<?php

declare(strict_types=1);

namespace App\Importer;

use App\Support\DateTimeParser;
use Doctrine\ORM\EntityManagerInterface;

use function file_get_contents;
use function str_ends_with;

/**
 * Импорт событий использования (JSONL).
 *
 * Дедупликация по (provider, provider_request_id): повторная доставка
 * уже известного события целиком игнорируется (INSERT ... ON CONFLICT DO NOTHING).
 * Некорректная строка — ImportException (команда завершится кодом 1).
 */
final class UsageImporter
{
    private const INSERT_SQL = <<<'SQL'
        INSERT INTO usage_events (
            provider, provider_request_id, model, "user", ts,
            tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
            rated, cost, applied_price_valid_from
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, FALSE, NULL, NULL)
        ON CONFLICT (provider, provider_request_id) DO NOTHING
        SQL;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ImportException
     */
    public function importFile(string $path): void
    {
        if (!is_readable($path)) {
            throw new ImportException("файл не найден или нечитаем: $path");
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException("не удалось прочитать файл: $path");
        }

        $lines = str_ends_with($raw, "\n") ? substr($raw, 0, -1) : $raw;
        $lines = str_replace("\r\n", "\n", $lines);
        $lines = $lines === '' ? [] : explode("\n", $lines);

        $params = [];
        foreach ($lines as $n => $line) {
            $lineNo = $n + 1;
            if (trim($line) === '') {
                continue; // пустые строки (в т.ч. завершающая) игнорируются
            }
            $spec = self::parseLine($line, $lineNo);
            $params[] = $spec;
        }

        $this->em->wrapInTransaction(function () use ($params): void {
            $conn = $this->em->getConnection();
            foreach ($params as $p) {
                $conn->executeStatement(self::INSERT_SQL, $p);
            }
        });
    }

    /**
     * @return list<array<string, mixed>> параметры вставки
     *
     * @throws ImportException
     */
    private static function parseLine(string $line, int $lineNo): array
    {
        $ctx = "строка $lineNo";
        $data = json_decode($line, true);
        if (!is_array($data)) {
            throw new ImportException("$ctx: некорректный JSON");
        }

        $provider = self::requireNonEmptyString($data, 'provider', $ctx);
        $requestId = self::requireNonEmptyString($data, 'provider_request_id', $ctx);
        $model = self::requireNonEmptyString($data, 'model', $ctx);
        $user = self::requireNonEmptyString($data, 'user', $ctx);
        $ts = DateTimeParser::parseUtc("$ctx.ts", self::requireNonEmptyString($data, 'ts', $ctx));

        $tokens = [];
        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $field) {
            if (!array_key_exists($field, $data) || !is_int($data[$field]) || $data[$field] < 0) {
                throw new ImportException("$ctx.$field: ожидается целое >= 0");
            }
            $tokens[$field] = $data[$field];
        }

        return [
            $provider,
            $requestId,
            $model,
            $user,
            $ts->format('Y-m-d H:i:s.u'),
            $tokens['tokens_input'],
            $tokens['tokens_output'],
            $tokens['tokens_cache_read'],
            $tokens['tokens_cache_write'],
        ];
    }

    /**
     * @param array<string, mixed> $row
     *
     * @throws ImportException
     */
    private static function requireNonEmptyString(array $row, string $field, string $ctx): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException("$ctx.$field: ожидается непустая строка");
        }

        return $value;
    }
}
