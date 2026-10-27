<?php

declare(strict_types=1);

namespace App\Billing\Import;

use App\Billing\Import\Dto\UsageEventInput;
use App\Billing\ValueObject\TokenUsage;

/**
 * Разбор и валидация JSONL-файла событий использования → DTO.
 *
 * Один объект на строку; пустые строки (включая завершающий перевод строки) игнорируются.
 *
 * @throws ImportException
 */
final class UsageFileParser
{
    /**
     * @return list<UsageEventInput>
     *
     * @throws ImportException
     */
    public function parse(string $content): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\n|\r/', $content) as $i => $line) {
            if (trim($line) === '') {
                continue;
            }
            $out[] = $this->parseLine($i + 1, $line);
        }

        return $out;
    }

    /**
     * @throws ImportException
     */
    private function parseLine(int $lineNumber, string $line): UsageEventInput
    {
        $context = sprintf('строка %d', $lineNumber);
        try {
            $data = json_decode($line, true, 64, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('%s: некорректный JSON: %s', $context, $e->getMessage()));
        }
        $data = Json::array($data, $context, nonEmpty: true);

        $provider = Json::stringField($data, 'provider', $context);
        $providerRequestId = Json::stringField($data, 'provider_request_id', $context);
        $model = Json::stringField($data, 'model', $context);
        $user = Json::stringField($data, 'user', $context);
        $ts = Json::utcDateTime($data['ts'] ?? null, $context, 'ts');

        $tokens = new TokenUsage(
            Json::nonNegativeInt($data['tokens_input'] ?? null, $context.'[tokens_input]'),
            Json::nonNegativeInt($data['tokens_output'] ?? null, $context.'[tokens_output]'),
            Json::nonNegativeInt($data['tokens_cache_read'] ?? null, $context.'[tokens_cache_read]'),
            Json::nonNegativeInt($data['tokens_cache_write'] ?? null, $context.'[tokens_cache_write]'),
        );

        return new UsageEventInput($provider, $providerRequestId, $model, $user, $ts, $tokens);
    }
}
