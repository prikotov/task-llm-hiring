<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели, действующая с valid_from (включительно).
 * Уникальность — по (модель, valid_from).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model', 'valid_from'])]
class PriceRow
{
    public const UNIT_PER_1M = 'per_1m_tokens';

    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model', referencedColumnName: 'id', onDelete: 'RESTRICT', nullable: false)]
    public Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    public string $currency = 'USD';

    /// @see \App\Domain\PriceSpec
    #[ORM\Column(type: 'string', length: 32)]
    public string $unit = '';

    /** Ставки как десятичные строки, ключи зависят от unit (input/output/cache_read/cache_write либо any). */
    #[ORM\Column(type: 'json')]
    public array $rates = [];
}
