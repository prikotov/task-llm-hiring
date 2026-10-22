<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine;

use App\Billing\Domain\Usage\CostAdjustment;
use App\Billing\Domain\Usage\UsageEvent;
use App\Billing\Domain\Usage\UsageJournal;
use App\Billing\Infrastructure\Persistence\Doctrine\Entity\CostAdjustmentRecord;
use App\Billing\Infrastructure\Persistence\Doctrine\Entity\UsageEventRecord;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUsageJournal implements UsageJournal
{
    /**
     * Кэш известных натуральных ключей: импорт больших файлов не должен
     * порождать SELECT на каждую строку.
     *
     * @var array<string, true>|null
     */
    private ?array $knownKeys = null;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function addIfAbsent(UsageEvent $event): bool
    {
        $this->knownKeys ??= $this->loadKnownKeys();
        $key = $event->naturalKey();

        if (isset($this->knownKeys[$key])) {
            return false;
        }

        $this->em->persist(UsageEventRecord::fromDomain($event));
        $this->em->flush();
        $this->knownKeys[$key] = true;

        return true;
    }

    public function unrated(): array
    {
        return array_map(
            static fn (UsageEventRecord $r): UsageEvent => $r->toDomain(),
            $this->em->getRepository(UsageEventRecord::class)->findBy(['ratedAt' => null], ['id' => 'ASC']),
        );
    }

    public function ratedByPriceRow(int $priceRowId): array
    {
        return array_map(
            static fn (UsageEventRecord $r): UsageEvent => $r->toDomain(),
            $this->em->getRepository(UsageEventRecord::class)->findBy(['priceRowId' => $priceRowId], ['id' => 'ASC']),
        );
    }

    public function saveRating(array $events): void
    {
        foreach ($events as $event) {
            $record = $this->em->find(UsageEventRecord::class, $event->id)
                ?? throw new \RuntimeException(sprintf('Событие #%s не найдено', (string) $event->id));

            $record->applyRating($event->rating);
        }

        $this->em->flush();
    }

    public function recordAdjustments(array $adjustments): void
    {
        foreach ($adjustments as $adjustment) {
            $this->em->persist(CostAdjustmentRecord::fromDomain($adjustment));
        }

        $this->em->flush();
    }

    public function all(): iterable
    {
        foreach ($this->em->getRepository(UsageEventRecord::class)->findBy([], ['id' => 'ASC']) as $record) {
            yield $record->toDomain();
        }
    }

    /**
     * @return array<string, true>
     */
    private function loadKnownKeys(): array
    {
        $known = [];

        $rows = $this->em->getConnection()->fetchAllAssociative(
            'SELECT provider_code, provider_request_id FROM usage_event',
        );

        foreach ($rows as $row) {
            $known[$row['provider_code']."\0".$row['provider_request_id']] = true;
        }

        return $known;
    }
}
