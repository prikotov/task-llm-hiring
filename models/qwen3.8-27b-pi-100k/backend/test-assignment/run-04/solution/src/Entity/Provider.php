<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    /** Идентификатор провайдера из входящих данных. */
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    public string $name = '';

    /**
     * Оф-пиковое окно (UTC, "ЧЧ:ММ"), действует для всех моделей провайдера.
     * Все три поля либо установлены одновременно, либо все NULL.
     */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakEnd = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
