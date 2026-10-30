<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Infrastructure\Doctrine\Repository\UsageEventRepository;
use App\Infrastructure\Json\UsageEventReader;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий. Дедупликация по (provider, provider_request_id):
 * повторная доставка игнорируется целиком, ошибка одной строки JSONL
 * отменяет весь файл (частичный приём не нужен и опасен).
 */
final class ImportUsageEvents
{
    public function __construct(
        private readonly UsageEventReader $reader,
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @return array{accepted: int, duplicates: int}
     */
    public function __invoke(string $path): array
    {
        $events = $this->reader->read($path);

        $existing = $this->events->existingKeys(
            array_map(static fn ($event): array => [$event->providerCode, $event->providerRequestId], $events),
        );

        $accepted = 0;
        $duplicates = 0;
        $seenInFile = [];

        $this->em->beginTransaction();
        try {
            foreach ($events as $event) {
                $key = $event->key();

                if (isset($existing[$key]) || isset($seenInFile[$key])) {
                    ++$duplicates;
                    continue;
                }

                $seenInFile[$key] = true;
                $this->events->persist($event);
                ++$accepted;
            }

            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }

        return ['accepted' => $accepted, 'duplicates' => $duplicates];
    }
}
