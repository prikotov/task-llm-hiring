<?php

declare(strict_types=1);

namespace App\Billing\Entity;

#[\Doctrine\ORM\Mapping\Entity]
#[\Doctrine\ORM\Mapping\Table(name: 'providers')]
#[\Doctrine\ORM\Mapping\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
class Provider
{
    /** Пустые строки не используются; code — натур. ключ. */
    public function __construct(
        #[\Doctrine\ORM\Mapping\Id]
        #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 64)]
        #[\Doctrine\ORM\Mapping\GeneratedValue(strategy: 'NONE')]
        public readonly string $code,
    ) {}

    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    public string $name = '';

    /** Minutes from UTC midnight, 0..1439; null — off-peak скидка не задана. */
    #[\Doctrine\ORM\Mapping\Column(name: 'off_peak_window_start', type: 'integer', nullable: true)]
    public ?int $offPeakStartMinutes = null;

    #[\Doctrine\ORM\Mapping\Column(name: 'off_peak_window_end', type: 'integer', nullable: true)]
    public ?int $offPeakEndMinutes = null;

    /** Десятичная строка bcmath (без float). */
    #[\Doctrine\ORM\Mapping\Column(name: 'off_peak_multiplier', type: 'string', length: 32, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
