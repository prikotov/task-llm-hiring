<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Хранилище событий использования.
 *
 * Dедупликация — на уровне загрузчика (первое вхождение побеждает);
 * здесь только селект всех вставленных событий.
 */
final class UsageRepository
{
    /** @var EntityRepository<UsageEvent>|null */
    private ?EntityRepository $repo;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return list<UsageEvent>
     */
    public function all(): array
    {
        $repo = $this->repo ??= ($this->em)->getRepository(UsageEvent::class);

        /** @var list<UsageEvent> $events */
        $events = $repo->findAll();

        return $events;
    }
}
