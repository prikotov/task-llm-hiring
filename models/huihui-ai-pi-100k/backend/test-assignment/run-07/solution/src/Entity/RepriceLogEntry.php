<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал ретро-коррекций цены: по каждому пересчитанному событию
 * записаны старая и новая стоимость и дельта. Нужен для аудита; отчёт
 * (app:billing:report) всегда показывает актуальные (новые) стоимости.
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_log')]
final class RepriceLogEntry
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: Types::BIGINT)]
        public int $id = 0,

        #[ORM\Column(type: Types::STRING, length: 64)]
        public string $providerCode,

        #[ORM\Column(type: Types::STRING, length: 255)]
        public string $providerRequestId,

        #[ORM\Column(type: Types::STRING, length: 30)]
        public string $oldCostUsd,

        #[ORM\Column(type: Types::STRING, length: 30)]
        public string $newCostUsd,

        #[ORM\Column(type: Types::STRING, length: 30)]
        public string $deltaUsd,

        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
        public \DateTimeImmutable $appliedAt,
    ) {
    }
}
