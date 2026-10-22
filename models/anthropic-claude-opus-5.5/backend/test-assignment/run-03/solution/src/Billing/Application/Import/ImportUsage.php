<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Domain\Usage\UsageJournal;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий использования.
 *
 * Дедупликация — по (provider, provider_request_id) на уровне журнала.
 * Код 0 даже если все строки оказались дублями.
 */
final class ImportUsage
{
    public function __construct(
        private readonly UsageJournal $journal,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(string $path): UsageImportResult
    {
        $events = (new UsageFileParser())->parse($path);

        $added = 0;
        $duplicates = 0;

        $this->em->wrapInTransaction(function () use ($events, &$added, &$duplicates): void {
            foreach ($events as $event) {
                if ($this->journal->addIfAbsent($event)) {
                    ++$added;
                } else {
                    ++$duplicates;
                }
            }
        });

        return new UsageImportResult(count($events), $added, $duplicates);
    }
}

final readonly class UsageImportResult
{
    public function __construct(
        public int $parsed,
        public int $added,
        public int $duplicates,
    ) {
    }
}
