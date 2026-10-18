<?php

declare(strict_types=1);

namespace App\Persistence\Entity;

use App\Domain\Common\Iso8601Utc as Utc;
use App\Domain\Pricing\PriceRowValue;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\TokenKind;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен: схема тарификации и ставки, действовавшие с valid_from.
 *
 * Ставки хранятся точными десятичными строками (текст, без float):
 * в SQL с деньгами не производится арифметика, только выборка.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_lookup', columns: ['provider_id', 'model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public Model $model;

    #[ORM\Column(name: 'valid_from', type: 'datetimetz')]
    public DateTimeInterface $validFrom;

    #[ORM\Column(type: 'text')]
    public string $currency = 'USD';

    #[ORM\Column(type: 'text')]
    public string $unit = '';

    #[ORM\Column(name: 'rate_input', type: 'text', nullable: true)]
    public ?string $rateInput = null;

    #[ORM\Column(name: 'rate_output', type: 'text', nullable: true)]
    public ?string $rateOutput = null;

    #[ORM\Column(name: 'rate_cache_read', type: 'text', nullable: true)]
    public ?string $rateCacheRead = null;

    #[ORM\Column(name: 'rate_cache_write', type: 'text', nullable: true)]
    public ?string $rateCacheWrite = null;

    #[ORM\Column(name: 'rate_any', type: 'text', nullable: true)]
    public ?string $rateAny = null;

    public function toValueObject(): PriceRowValue
    {
        return new PriceRowValue(
            $this->provider->code,
            $this->model->code,
            Utc::reanchor($this->validFrom),
            $this->currency,
            PriceUnit::from($this->unit),
            array_filter([
                TokenKind::Input->value => $this->rateInput,
                TokenKind::Output->value => $this->rateOutput,
                TokenKind::CacheRead->value => $this->rateCacheRead,
                TokenKind::CacheWrite->value => $this->rateCacheWrite,
                TokenKind::Any->value => $this->rateAny,
            ], static fn (?string $rate): bool => $rate !== null),
        );
    }
}
