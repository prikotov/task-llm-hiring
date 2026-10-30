<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Exception\BillingException;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий использования из JSONL-файла.
 * Дедупликация по (provider, provider_request_id): повторяющиеся строки
 * игнорируются целиком, ошибки не поднимаются (TASK.md п. 5).
 */
final class UsageImportService
{
    private const TOKEN_KEYS = [
        'tokens_input' => 'input',
        'tokens_output' => 'output',
        'tokens_cache_read' => 'cache_read',
        'tokens_cache_write' => 'cache_write',
    ];

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return array{written:int,skipped:int}
     */
    public function import(string $filePath): array
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw BillingException::parseError(sprintf('Файл не найден или не читается: %s', $filePath));
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw BillingException::parseError(sprintf('Не удалось прочитать: %s', $filePath));
        }

        $repo = $this->em->getRepository(UsageEvent::class);
        $written = 0;
        $skipped = 0;
        $line = 0;
        foreach ($lines as $raw) {
            $line++;
            $raw = trim((string) $raw);
            if ($raw === '') {
                continue;
            }
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw BillingException::parseError(sprintf('Строка %d: ожидался объект JSON, получено другое', $line));
            }
            try {
                $ev = $this->normalize($data);
            } catch (BillingException $e) {
                throw BillingException::parseError(sprintf('Строка %d: %s', $line, $e->getMessage()));
            }

            $existing = $repo->findOneBy([
                'provider' => $ev->provider,
                'providerRequestId' => $ev->requestId,
            ]);
            if ($existing !== null) {
                $skipped++;
                continue;
            }

            $entity = new UsageEvent(
                $ev->provider,
                $ev->requestId,
                $ev->model,
                $ev->user,
                $ev->ts,
                $ev->tokens['input'],
                $ev->tokens['output'],
                $ev->tokens['cache_read'],
                $ev->tokens['cache_write'],
            );
            $this->em->persist($entity);
            $written++;
        }

        $this->em->flush();

        return ['written' => $written, 'skipped' => $skipped];
    }

    /** @param array<string,mixed> $data */
    private function normalize(array $data): object
    {
        foreach ([
            'provider',
            'provider_request_id',
            'model',
            'user',
            'ts',
        ] as $k) {
            if (!array_key_exists($k, $data) || !is_string($data[$k]) || $data[$k] === '') {
                throw BillingException::parseError(sprintf('отсутствует или не строка: "%s"', $k));
            }
        }

        $ts = self::parseIsoUtz($data['ts']);
        $tokens = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0];
        foreach (self::TOKEN_KEYS as $jsonKey => $internal) {
            $raw = $data[$jsonKey] ?? 0;
            if (!is_int($raw)) {
                throw BillingException::parseError(sprintf('%s: ожидается целое >= 0', $jsonKey));
            }
            if ($raw < 0) {
                throw BillingException::parseError(sprintf('%s: ожидается целое >= 0, получено %d', $jsonKey, $raw));
            }
            $tokens[$internal] = $raw;
        }

        return (object) [
            'provider' => $data['provider'],
            'requestId' => $data['provider_request_id'],
            'model' => $data['model'],
            'user' => $data['user'],
            'ts' => $ts,
            'tokens' => $tokens,
        ];
    }

    private static function parseIsoUtz(string $iso): \DateTimeImmutable
    {
        // строим DateImmutable строго по ISO-8601 с UTC
        $patterns = [
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/i',
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/i',
        ];
        if (preg_match($patterns[0], $iso) || preg_match($patterns[1], $iso)) {
            try {
                return new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
            } catch (\Exception $e) {
                throw BillingException::parseError(sprintf('Некорректная ISO-8601 дата: %s', $iso));
            }
        }
        throw BillingException::parseError(sprintf('Ожидается ISO-8601 UTC (например 2026-09-15T11:00:00Z), получено: %s', $iso));
    }
}
