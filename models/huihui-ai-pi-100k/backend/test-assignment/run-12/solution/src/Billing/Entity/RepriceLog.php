<?php

declare(strict_types=1);

namespace App\Billing\Entity;

#[\Doctrine\ORM\Mapping\Entity]
#[\Doctrine\ORM\Mapping\Table(name: 'reprice_logs')]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_reprice_applied', columns: ['applied_at'])]
class RepriceLog
{
    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue(strategy: 'AUTO')]
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    public ?int $id = null;

    #[\Doctrine\ORM\Mapping\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $appliedAt;

    /**
     * @var list<string> список строк «provider|model@valid_from» затронутых коррекций.
     */
    #[\Doctrine\ORM\Mapping\Column(name: 'affected_units', type: 'json')]
    public array $affectedUnits = [];

    /** Кол-во пересчитанных событий. */
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    public int $events = 0;

    /** new_sum − old_sum за пересчитанные события, decimal. */
    #[\Doctrine\ORM\Mapping\Column(name: 'delta_usd', type: 'string', length: 32)]
    public string $deltaUsd = '0';

    #[\Doctrine\ORM\Mapping\Column(name: 'old_sum_usd', type: 'string', length: 32)]
    public string $oldSumUsd = '0';

    #[\Doctrine\ORM\Mapping\Column(name: 'new_sum_usd', type: 'string', length: 32)]
    public string $newSumUsd = '0';
}
