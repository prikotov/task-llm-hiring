<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\IsoTime;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования из JSONL (дедупликация см. п.5 TASK.md).
 */
final class UsageEventService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     *
     * @return array{new: int, duplicate: int}
     */
    public function import(array $rows): array
    {
        $new = 0;
        $duplicate = 0;

        foreach ($rows as $row) {
            $event = $this->parseEvent($row);

            $existing = $this->events->findByKey($event->getProvider(), $event->getProviderRequestId());
            if ($existing !== null) {
                // Повторная доставка уже известного события — игнорируем целиком.
                ++$duplicate;

                continue;
            }

            $this->em->persist($event);
            ++$new;
        }

        $this->em->flush();

        return ['new' => $new, 'duplicate' => $duplicate];
    }

    /**
     * @param array<string, mixed> $row
     */
    private function parseEvent(array $row): UsageEvent
    {
        foreach (['provider', 'provider_request_id', 'model', 'user', 'ts'] as $field) {
            if (!isset($row[$field]) || $row[$field] === '' || $row[$field] === null) {
                throw new \RuntimeException("usage.import: event missing field '{$field}'");
            }
        }

        $tokens = [
            'input' => (int) ($row['tokens_input'] ?? 0),
            'output' => (int) ($row['tokens_output'] ?? 0),
            'cache_read' => (int) ($row['tokens_cache_read'] ?? 0),
            'cache_write' => (int) ($row['tokens_cache_write'] ?? 0),
        ];
        foreach ($tokens as $label => $value) {
            if ($value < 0) {
                throw new \RuntimeException("usage.import: tokens_{$label} must be >= 0");
            }
        }

        $ts = IsoTime::parseUtc((string) $row['ts']);

        $event = new UsageEvent();
        $event->setImport(
            (string) $row['provider'],
            (string) $row['provider_request_id'],
            (string) $row['model'],
            (string) $row['user'],
            $ts,
            $tokens['input'],
            $tokens['output'],
            $tokens['cache_read'],
            $tokens['cache_write'],
        );

        return $event;
    }
}
