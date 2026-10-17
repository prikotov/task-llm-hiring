<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_pricerow_model_validfrom', columns: ['model_id', 'valid_from'])]
class PriceRow
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    public const RATES_PER_TOKEN = ['input', 'output', 'cache_read', 'cache_write'];
    public const RATE_UNIFIED = 'any';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    public Model $model;

    /** Момент, с которого действуют цены (включительно). */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    public string $currency = 'USD';

    /** per_1m_tokens | per_1m_tokens_unified */
    #[ORM\Column(type: 'string', length: 32)]
    public string $unit;

    /** Суммы в виде {вид: десятичная строка}. */
    #[ORM\Column(type: Types::JSON)]
    public array $rates;
}
