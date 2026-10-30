<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\DomainException;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use App\Support\IsoTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий использования (JSONL).
 *
 * Идемпотентность приёма (п. 5 задания): дедупликация по естественному
 * ключу (provider, provider_request_id). Уже известное событие —
 * целиком игнорируется (никаких обновлений и доначислений).
 * Новая строка всегда попадает в статус "unrated" — расчёт делает
 * app:billing:rate (цены могли появиться позже события).
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /** @return array{imported: int, skippedDuplicates: int} */
    public function import(string $file): array
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new DomainException(sprintf('Файл "%s" не найден или недоступен', $file));
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new DomainException(sprintf('Не удалось прочитать файл "%s"', $file));
        }

        $imported = 0;
        $skipped = 0;

        $this->em->wrapInTransaction(function () use ($lines, &$imported, &$skipped): void {
            foreach ($lines as $lineNo => $line) {
                $where = sprintf('строка %d', $lineNo + 1);
                $raw = json_decode($line, true);
                if (!is_array($raw)) {
                    throw new DomainException(sprintf('%s: некорректный JSON', $where));
                }
                $event = $this->buildEvent($raw, $where);

                $existing = $this->events->findByProviderRequest($event->provider, $event->providerRequestId);
                if ($existing !== null) {
                    ++$skipped;
                    continue;
                }

                $this->em->persist($event);
                ++$imported;
            }
        });

        return ['imported' => $imported, 'skippedDuplicates' => $skipped];
    }

    /** @param array<string, mixed> $raw */
    private function buildEvent(array $raw, string $where): UsageEvent
    {
        $provider = self::stringField($raw, 'provider', $where);
        $requestId = self::stringField($raw, 'provider_request_id', $where);
        $model = self::stringField($raw, 'model', $where);
        $user = self::stringField($raw, 'user', $where);
        $ts = IsoTime::parseUtc(self::stringField($raw, 'ts', $where), $where);

        $event = new UsageEvent();
        $event->provider = $provider;
        $event->providerRequestId = $requestId;
        $event->model = $model;
        $event->user = $user;
        $event->ts = $ts;
        $event->tokensInput = self::tokenField($raw, 'tokens_input', $where);
        $event->tokensOutput = self::tokenField($raw, 'tokens_output', $where);
        $event->tokensCacheRead = self::tokenField($raw, 'tokens_cache_read', $where);
        $event->tokensCacheWrite = self::tokenField($raw, 'tokens_cache_write', $where);

        return $event;
    }

    /** @param array<string, mixed> $raw */
    private static function tokenField(array $raw, string $field, string $where): int
    {
        $value = $raw[$field] ?? null;
        if (is_int($value) && $value >= 0) {
            return $value;
        }
        // Допускаем целые, пришедшие строкой (например из другого конвейера),
        // но не float и не отрицательные.
        if (is_string($value) && preg_match('/^\d+$/', $value)) {
            return (int) $value;
        }

        throw new DomainException(sprintf('%s: поле "%s" — целое >= 0', $where, $field));
    }

    /** @param array<string, mixed> $raw */
    private static function stringField(array $raw, string $field, string $where): string
    {
        $value = $raw[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new DomainException(sprintf('%s: поле "%s" — непустая строка', $where, $field));
        }

        return $value;
    }
}
