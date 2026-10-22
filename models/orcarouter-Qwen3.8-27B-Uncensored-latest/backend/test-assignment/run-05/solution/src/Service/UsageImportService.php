<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\EventStatus;
use App\Support\ImportError;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий использования из JSONL (по одному событию на строку).
 *
 * Идемпотентен по ключу (provider, provider_request_id): повторная доставка
 * уже известного события молча игнорируется.
 */
final class UsageImportService
{
    private const INSERT_SQL = <<<SQL
        INSERT INTO billing_usage_event
            (provider, provider_request_id, model, "user", ts,
             tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (provider, provider_request_id) DO NOTHING
        SQL;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @return array{inserted: int, skipped: int} */
    public function import(string $path): array
    {
        if (!is_file($path)) {
            throw new ImportError('Файл не найден: '.$path);
        }
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new ImportError('Не удалось открыть файл: '.$path);
        }

        $inserted = 0;
        $skipped = 0;
        $lineNo = 0;

        $this->em->getConnection()->beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $lineNo++;
                if (trim($line) === '') {
                    continue; // пустые строки (в т.ч. финальный перевод строки) пропускаем
                }
                $event = self::parseLine($line, $lineNo);
                $affected = $this->em->getConnection()->executeStatement(self::INSERT_SQL, [
                    $event['provider'],
                    $event['id'],
                    $event['model'],
                    $event['user'],
                    $event['ts']->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s.u'),
                    $event['in'],
                    $event['out'],
                    $event['cr'],
                    $event['cw'],
                    EventStatus::Unrated->value,
                ]);
                if ($affected > 0) {
                    $inserted++;
                } else {
                    $skipped++;
                }
            }
            $this->em->getConnection()->commit();
        } catch (\Throwable $e) {
            if ($this->em->getConnection()->isTransactionActive()) {
                $this->em->getConnection()->rollBack();
            }
            throw $e instanceof ImportError ? $e : new ImportError('Ошибка при импорте: '.$e->getMessage());
        } finally {
            fclose($handle);
        }

        return ['inserted' => $inserted, 'skipped' => $skipped];
    }

    /**
     * Распознаёт и валидирует строку JSONL.
     *
     * @return array{provider: string, id: string, model: string, user: string, ts: \DateTimeImmutable, in: string, out: string, cr: string, cw: string}
     *
     * @throws ImportError
     */
    private static function parseLine(string $line, int $lineNo): array
    {
        $label = sprintf('строка %d', $lineNo);
        try {
            $raw = json_decode($line, true, 512, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\JsonException $e) {
            throw new ImportError(sprintf('%s: некорректный JSON: %s', $label, $e->getMessage()));
        }
        if (!is_array($raw)) {
            throw new ImportError($label.': событие должно быть JSON-объектом');
        }

        $requireString = static fn (string $key): string => match (true) {
            !isset($raw[$key]) => throw new ImportError(sprintf('%s: отсутствует поле "%s"', $label, $key)),
            !is_string($raw[$key]) || $raw[$key] === '' => throw new ImportError(sprintf('%s: поле "%s" должно быть непустой строкой', $label, $key)),
            default => $raw[$key],
        };

        $provider = $requireString('provider');
        $requestId = $requireString('provider_request_id');
        $model = $requireString('model');
        $user = $requireString('user');
        $ts = $requireString('ts');

        try {
            $tsUtc = (new \DateTimeImmutable($ts, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ImportError(sprintf('%s: ts "%s" — ожидался ISO-8601: %s', $label, $ts, $e->getMessage()));
        }

        $token = static function (string $key) use ($label, $raw): string {
            $value = $raw[$key] ?? 0;
            if ((!is_int($value) || $value < 0) && !is_string($value)) {
                throw new ImportError(sprintf('%s: поле "%s" должно быть целым числом >= 0', $label, $key));
            }
            if (is_string($value) && !preg_match('/^\d+$/', $value)) {
                throw new ImportError(sprintf('%s: поле "%s" должно быть целым числом >= 0', $label, $key));
            }

            return (string) $value;
        };

        return [
            'provider' => $provider,
            'id' => $requestId,
            'model' => $model,
            'user' => $user,
            'ts' => $tsUtc,
            'in' => $token('tokens_input'),
            'out' => $token('tokens_output'),
            'cr' => $token('tokens_cache_read'),
            'cw' => $token('tokens_cache_write'),
        ];
    }
}
