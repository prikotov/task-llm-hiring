<?php

declare(strict_types=1);

namespace App\Domain\Inbound;

use App\Exception\InvalidInputException;

/**
 * Парсер JSONL-событий использования.
 */
final class UsageInputParser
{
    /**
     * Разбирает строки JSONL.
     *
     * @throws InvalidInputException
     */
    public static function parse(string $jsonl): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($jsonl)) ?: [];
        $result = [];
        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $decoded = json_decode($line, true);
            if (!is_array($decoded)) {
                throw new InvalidInputException(sprintf('usage line %d: invalid JSON.', $i + 1));
            }
            $result[] = self::parseOne($decoded, $i + 1);
        }

        return $result;
    }

    private static function parseOne(array $d, int $lineNo): array
    {
        $required = ['provider', 'provider_request_id', 'model', 'user', 'ts'];
        foreach ($required as $k) {
            if (empty($d[$k]) || !is_string($d[$k])) {
                throw new InvalidInputException(sprintf('line %d: missing "%s".', $lineNo, $k));
            }
        }

        $tsRaw = $d['ts'];
        if (!str_ends_with($tsRaw, 'Z')) {
            throw new InvalidInputException(sprintf('line %d: ts must end with "Z".', $lineNo));
        }
        try {
            $ts = new \DateTimeImmutable($tsRaw, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new InvalidInputException(sprintf('line %d: ts "%s" is not ISO-8601.', $lineNo, $tsRaw));
        }

        $tokens = [
            'tokens_input' => self::int($d, 'tokens_input', $lineNo),
            'tokens_output' => self::int($d, 'tokens_output', $lineNo),
            'tokens_cache_read' => self::int($d, 'tokens_cache_read', $lineNo),
            'tokens_cache_write' => self::int($d, 'tokens_cache_write', $lineNo),
        ];
        foreach ($tokens as $v) {
            if ($v < 0) {
                throw new InvalidInputException('line ' . $lineNo . ': token counts must be >= 0.');
            }
        }

        return [
            'provider' => $d['provider'],
            'provider_request_id' => $d['provider_request_id'],
            'model' => $d['model'],
            'user' => $d['user'],
            'ts' => $ts,
            'tokens' => $tokens,
        ];
    }

    private static function int(array $d, string $key, int $lineNo): int
    {
        $value = $d[$key] ?? 0;
        if (!is_int($value)) {
            throw new InvalidInputException(sprintf('line %d: "%s" must be an integer.', $lineNo, $key));
        }

        return $value;
    }
}
