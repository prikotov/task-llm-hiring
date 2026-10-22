<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;

final class UsageImporter
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function import(string $file): int
    {
        $stream = @fopen($file, 'rb');
        if ($stream === false) {
            throw new InvalidArgumentException(sprintf('Не удалось открыть файл "%s"', $file));
        }

        $this->connection->beginTransaction();
        $imported = 0;
        $lineNumber = 0;
        try {
            while (($line = fgets($stream)) !== false) {
                ++$lineNumber;
                if (trim($line) === '') {
                    continue;
                }
                $context = sprintf('%s:%d', $file, $lineNumber);
                $event = InputData::decodeObject($line, $context);
                $provider = InputData::requiredString($event, 'provider', $context);
                $requestId = InputData::requiredString($event, 'provider_request_id', $context);
                $model = InputData::requiredString($event, 'model', $context);
                $user = InputData::requiredString($event, 'user', $context);
                $occurredAt = InputData::sqlTimestamp(InputData::utcTimestamp($event['ts'] ?? null, $context.' ts'));
                $tokens = [];
                foreach ([
                    'tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write',
                ] as $field) {
                    $value = $event[$field] ?? null;
                    if (!is_int($value) || $value < 0) {
                        throw new InvalidArgumentException(sprintf('%s: поле "%s" должно быть целым числом >= 0', $context, $field));
                    }
                    $tokens[$field] = $value;
                }

                $imported += $this->connection->executeStatement(<<<'SQL'
INSERT INTO usage_events (
    provider_code, provider_request_id, model_code, user_id, occurred_at,
    tokens_input, tokens_output, tokens_cache_read, tokens_cache_write
) VALUES (
    :provider, :request_id, :model, :user, :occurred_at,
    :tokens_input, :tokens_output, :tokens_cache_read, :tokens_cache_write
)
ON CONFLICT (provider_code, provider_request_id) DO NOTHING
SQL, [
                    'provider' => $provider,
                    'request_id' => $requestId,
                    'model' => $model,
                    'user' => $user,
                    'occurred_at' => $occurredAt,
                    ...$tokens,
                ]);
            }
            if (!feof($stream)) {
                throw new InvalidArgumentException(sprintf('Ошибка чтения файла "%s"', $file));
            }
            $this->connection->commit();
            return $imported;
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        } finally {
            fclose($stream);
        }
    }
}
