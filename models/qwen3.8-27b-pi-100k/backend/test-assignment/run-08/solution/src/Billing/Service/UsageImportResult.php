<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Entity\UsageEvent;
use App\Billing\Import\Dto\UsageEventInput;
use App\Billing\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/** Результат импорта событий: принято / проигнорировано дубли. */
final class UsageImportResult
{
    public function __construct(
        public readonly int $accepted,
        public readonly int $duplicatesIgnored,
    ) {
    }
}
