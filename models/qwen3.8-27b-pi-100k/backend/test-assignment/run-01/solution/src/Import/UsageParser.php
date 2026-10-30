<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\IsoTime;
use stdClass;

/** Разбор и валидация JSONL-файла событий использования (по одной строке на событие). */
final class UsageParser extends AbstractJsonParser
{
    public function parseLine(string $rawLine, int $lineNumber): UsageEventLine
    {
        $ctx = sprintf('строка %d', $lineNumber);

        try {
            $decoded = json_decode($rawLine, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('%s: некорректный JSON — %s', $ctx, $e->getMessage()));
        }
        if (!($decoded instanceof \stdClass)) {
            throw new ImportException('%s: событие должно быть объектом JSON.', $ctx);
        }

        $provider = $this->string($decoded, 'provider', $ctx);
        $requestId = $this->string($decoded, 'provider_request_id', $ctx);
        $model = $this->string($decoded, 'model', $ctx);
        $user = $this->string($decoded, 'user', $ctx);

        $tsRaw = $this->string($decoded, 'ts', $ctx);
        try {
            $ts = IsoTime::parseUtc($tsRaw);
        } catch (\ValueError $e) {
            throw new ImportException(sprintf('%s: ts — %s', $ctx, $e->getMessage()));
        }

        return new UsageEventLine(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $this->nonNegativeInt($decoded, 'tokens_input', $ctx),
            $this->nonNegativeInt($decoded, 'tokens_output', $ctx),
            $this->nonNegativeInt($decoded, 'tokens_cache_read', $ctx),
            $this->nonNegativeInt($decoded, 'tokens_cache_write', $ctx),
        );
    }

    /**
     * Разбирает весь файл: пустые строки игнорируются, первая некорректная
     * строка — ошибка (код 1), ничего в БД при этом не попадает.
     *
     * @return list<UsageEventLine>
     */
    public function parseFile(string $content): array
    {
        $lines = [];
        $rawLines = preg_split('/\R/', $content) ?: [];
        foreach ($rawLines as $i => $raw) {
            $line = trim((string) $raw);
            if ($line === '') {
                continue;
            }

            $lines[] = $this->parseLine($line, $i + 1);
        }

        return $lines;
    }
}
