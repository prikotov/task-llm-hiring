<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер: код, имя и (опционально) оф-пиковое окно скидки UTC.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $code = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    public string $name = '';

    /** Граница окна скидок, формат "ЧЧ:ММ", UTC. */
    #[ORM\Column(name: 'off_peak_start', type: Types::STRING, length: 5, nullable: true)]
    public ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: Types::STRING, length: 5, nullable: true)]
    public ?string $offPeakEnd = null;

    /** Множитель стоимости в окне скидок (десятичная строка). */
    #[ORM\Column(name: 'off_peak_multiplier', type: Types::STRING, length: 32, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
