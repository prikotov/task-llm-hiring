<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Application\Parse\JsonInput;
use App\Domain\Billing\TokenCounts;
use App\Exception\InvalidUsageFileException;

/**
 * Разбор JSONL-файла событий использования.
 *
 * Правила:
 *  - одно событие на строку; пустые строки игнорируются;
 *  - обязательные поля: provider, provider_request_id, model, user,
 *    ts (ISO-8601 UTC), tokens_input/output/cache_read/cache_write (int ≥ 0);
 *  - строка, нарушающая формат, — ошибка всего файла (код завершения 1).
 *
 * @return UsageEventSpec[]
 */
final class UsageFileParser
{
    /**
     * @throws InvalidUsageFileException
     */
    public function parse(string $content): array
    {
        $lines = preg_split('/\R/', $content) ?: [];
        $specs = [];

        foreach ($lines as $index => $rawLine) {
            $line = (int) $index + 1;
            $text = trim($rawLine);
            if ($text === '') {
                continue;
            }

            try {
                $data = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new InvalidUsageFileException(sprintf('line %d: invalid JSON: %s', $line, $e->getMessage()));
            }

            if (!is_array($data)) {
                throw new InvalidUsageFileException(sprintf('line %d: must be a JSON object', $line));
            }

            $ctx = sprintf('line %d', $line);
            $provider = JsonInput::requiredString($data, 'provider', $ctx, InvalidUsageFileException::class);
            $requestId = JsonInput::requiredString($data, 'provider_request_id', $ctx, InvalidUsageFileException::class);
            $model = JsonInput::requiredString($data, 'model', $ctx, InvalidUsageFileException::class);
            $user = JsonInput::requiredString($data, 'user', $ctx, InvalidUsageFileException::class);
            $ts = JsonInput::isoUtcDateTime($data, 'ts', $ctx, InvalidUsageFileException::class);

            $tokens = new TokenCounts(
                JsonInput::nonNegativeInt($data, 'tokens_input', $ctx, InvalidUsageFileException::class),
                JsonInput::nonNegativeInt($data, 'tokens_output', $ctx, InvalidUsageFileException::class),
                JsonInput::nonNegativeInt($data, 'tokens_cache_read', $ctx, InvalidUsageFileException::class),
                JsonInput::nonNegativeInt($data, 'tokens_cache_write', $ctx, InvalidUsageFileException::class),
            );

            $specs[] = new UsageEventSpec($line, $provider, $requestId, $model, $user, $ts, $tokens);
        }

        if ($specs === []) {
            throw new InvalidUsageFileException('file contains no usage events (one JSON object per line)');
        }

        return $specs;
    }
}
