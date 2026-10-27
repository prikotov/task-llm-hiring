<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\BillingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ставка одной строки цены на конкретный вид токена.
 *
 * Естественный ключ — (price_row, kind).
 */
#[ORM\Entity(repositoryClass: BillingRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_price_rate_row_kind', columns: ['price_row_id', 'kind'])]
#[ORM\Index(name: 'idx_price_rate_row', fields: ['priceRow'])]
class PriceRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PriceRow::class, inversedBy: 'rates')]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: false)]
    private PriceRow $priceRow;

    /** input | output | cache_read | cache_write | any */
    #[ORM\Column(type: 'string', length: 16)]
    private string $kind;

    /** Десятичная строка (decimal), не float. */
    #[ORM\Column(type: 'decimal', precision: 24, scale: 16)]
    private string $rate = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceRow(): PriceRow
    {
        return $this->priceRow;
    }

    public function setPriceRow(PriceRow $priceRow): self
    {
        $this->priceRow = $priceRow;

        return $this;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}
