<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены: тариф модели, действующий с момента valid_from.
 * Естественный ключ — (model, valid_from): у модели может быть несколько
 * строк, при тарификации выбирается строка с наибольшим valid_from <= ts события.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    /** Зарезервировано на будущее; по условию везде USD. */
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', enumType: PricingUnit::class)]
    private PricingUnit $unit;

    /**
     * Ключ -> десятичная строка цены (float в ходу не бывает).
     * per_1m_tokens: input/output/cache_read/cache_write;
     * per_1m_tokens_unified: any.
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: 'json')]
    private array $rates;

    #[ORM\ManyToOne(Model::class, inversedBy: 'priceRows', fetch: 'EAGER')]
    private Model $model;

    public function __construct(Model $model, \DateTimeImmutable $validFrom, string $currency, PricingUnit $unit, array $rates)
    {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function unit(): PricingUnit
    {
        return $this->unit;
    }

    public function rate(string $kind): ?string
    {
        return $this->rates[$kind] ?? null;
    }

    /**
     * При апсите (импорт/ретро-коррекция) заменяет параметры строки.
     *
     * @param array<string, string> $rates
     */
    public function apply(string $currency, PricingUnit $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }
}
