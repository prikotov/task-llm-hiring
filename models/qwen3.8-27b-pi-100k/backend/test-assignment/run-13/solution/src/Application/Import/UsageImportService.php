<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Infrastructure\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use UnexpectedValueException;

/**
 * Импорт событий использования (JSONL).
 *
 * Повторная доставка событий (provider, provider_request_id) игнорируется.
 * Некорректная строка — исключение.
 */
final readonly class UsageImportService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{inserted: int, duplicates: int}
     *
     * @throws JsonException
     */
    public function importFile(string $file): array
    {
        if (! is_file($file) || ! is_readable($file)) {
            throw new UnexpectedValueException(sprintf('Файл не найден: "%s"', $file));
        }

        $handle = fopen($file, 'rb');
        if ($handle === false) {
            throw new UnexpectedValueException(sprintf('Не удалось открыть файл: "%s"', $file));
        }

        $inserted = 0;
        $duplicates = 0;
        $lineNo = 0;
        $events = [];

        try {
            while (($line = fgets($handle)) !== false) {
                $lineNo += 1;
                $trimmed = trim($line);
                if ($trimmed === '') {
                    continue;
                }

                try {
                    $raw = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
                    if (! is_array($raw)) {
                        throw new UnexpectedValueException(sprintf('Строка %d: ожидался объект JSON', $lineNo));
                    }
                    $event = self::toEvent($lineNo, $raw);
                } catch (JsonException) {
                    throw new \InvalidArgumentException(sprintf('Строка %d: некорректный JSON', $lineNo));
                }

                // Проверяем дубликат
                $existing = $this->em->getRepository(UsageEvent::class)
                    ->findOneBy([
                        'provider' => $event['provider'],
                        'requestId' => $event['requestId'],
                    ]);

                if ($existing !== null) {
                    $duplicates += 1;
                    continue;
                }

                $this->em->persist(new UsageEvent(
                    provider: $event['provider'],
                    requestId: $event['requestId'],
                    model: $event['model'],
                    user: $event['user'],
                    ts: $event['ts'],
                    tokensInput: $event['tokensInput'],
                    tokensOutput: $event['tokensOutput'],
                    tokensCacheRead: $event['tokensCacheRead'],
                    tokensCacheWrite: $event['tokensCacheWrite'],
                ));
                $events[] = $event;
                $inserted += 1;
            }

            $this->em->flush();

            return [
                'inserted' => $inserted,
                'duplicates' => $duplicates,
            ];
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param array $raw
     *
     * @return array{provider: string, requestId: string, model: string, user: string, ts: \DateTimeImmutable, tokensInput: int, tokensOutput: int, tokensCacheRead: int, tokensCacheWrite: int}
     */
    private static function toEvent(int $lineNo, array $raw): array
    {
        $provider = self::requiredString($raw, 'provider', $lineNo);
        $requestId = self::requiredString($raw, 'provider_request_id', $lineNo);
        $model = self::requiredString($raw, 'model', $lineNo);
        $user = self::requiredString($raw, 'user', $lineNo);

        $tsRaw = self::requiredString($raw, 'ts', $lineNo);
        try {
            $ts = new \DateTimeImmutable($tsRaw, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new UnexpectedValueException(sprintf('Строка %d: некорректная дата "%s"', $lineNo, $tsRaw));
        }

        return [
            'provider' => $provider,
            'requestId' => $requestId,
            'model' => $model,
            'user' => $user,
            'ts' => $ts,
            'tokensInput' => self::requiredToken($raw, 'tokens_input', $lineNo),
            'tokensOutput' => self::requiredToken($raw, 'tokens_output', $lineNo),
            'tokensCacheRead' => self::requiredToken($raw, 'tokens_cache_read', $lineNo),
            'tokensCacheWrite' => self::requiredToken($raw, 'tokens_cache_write', $lineNo),
        ];
    }

    /**
     * @param array $raw
     */
    private static function requiredString(array $raw, string $key, int $lineNo): string
    {
        if (! array_key_exists($key, $raw)) {
            throw new UnexpectedValueException(sprintf('Строка %d: отсутствует "%s"', $lineNo, $key));
        }
        $value = $raw[$key];
        if (! is_string($value) || trim($value) === '') {
            throw new UnexpectedValueException(sprintf('Строка %d: "%s" должна быть строкой', $lineNo, $key));
        }

        return $value;
    }

    /**
     * @param array $raw
     */
    private static function requiredToken(array $raw, string $key, int $lineNo): int
    {
        if (! array_key_exists($key, $raw)) {
            throw new UnexpectedValueException(sprintf('Строка %d: отсутствует "%s"', $lineNo, $key));
        }
        $value = $raw[$key];
        if (! is_int($value) || $value < 0) {
            throw new UnexpectedValueException(sprintf('Строка %d: "%s" должно быть целым >= 0', $lineNo, $key));
        }

        return $value;
    }
}
