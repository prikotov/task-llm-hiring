<?php

declare(strict_types=1);

namespace App\Application\Parsers;

use App\Domain\Exception\ImportException;

/** Разбор одной строки JSONL события использования. */
final class UsageEventLine
{
    private const TOKEN_FIELDS = ['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'];

    /**
     * @return array{
     *   provider: string, provider_request_id: string, model: string, user: string,
     *   ts: \DateTimeImmutable, tokens: array{input:int, output:int, cache_read:int, cache_write:int}
     * }
     */
    public static function fromJsonLine(string $json, int $lineNumber): array
    {
        $context = sprintf('строка %d', $lineNumber);

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw ImportException::invalid($context, 'ожидается JSON-объект события');
        }

        $tokens = [];
        foreach (self::TOKEN_FIELDS as $i => $field) {
            $key = ['input', 'output', 'cache_read', 'cache_write'][$i];
            $tokens[$key] = Scalar::tokenCount($decoded[$field] ?? null, $field, $context);
        }

        return [
            'provider' => Scalar::nonEmptyString($decoded['provider'] ?? null, 'provider', $context),
            'provider_request_id' => Scalar::nonEmptyString($decoded['provider_request_id'] ?? null, 'provider_request_id', $context),
            'model' => Scalar::nonEmptyString($decoded['model'] ?? null, 'model', $context),
            'user' => Scalar::nonEmptyString($decoded['user'] ?? null, 'user', $context),
            'ts' => Scalar::isoUtc($decoded['ts'] ?? null, $context),
            'tokens' => $tokens,
        ];
    }
}
