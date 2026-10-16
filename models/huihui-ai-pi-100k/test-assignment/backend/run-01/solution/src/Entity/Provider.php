<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    /** Код провайдера (естественный ключ). */
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    public string $code;

    #[ORM\Column(type: 'string', length: 255)]
    public string $name;

    /**
     * Начало оф-пик окна в минутах от полуночи UTC включительно (null — окно не задано).
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $offPeakWindowStart = null;

    /**
     * Конец оф-пик окна в минутах от полуночи UTC, исключительно.
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $offPeakWindowEnd = null;

    /**
     * Множитель стоимости в оф-пике, десятичная строка.
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
