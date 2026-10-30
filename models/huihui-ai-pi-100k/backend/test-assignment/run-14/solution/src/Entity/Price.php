<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\PricingUnit;

/**
 * Строка цены модели, действующая с момента `validFrom` (включительно).
 *
 * Естественный ключ: (model, validFrom) — upsert по этой паре.
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_prices_model_valid_from', columns: ['model_id', 'valid_from']),
], indexes: [
    new ORM\Index(name: 'idx_prices_model_valid', columns: ['model_id', 'valid_from']),
])]
final class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    /** Момент действия (UTC, включительно). */
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => '1970-01-01 00:00:00'])]
    private \DateTimeInterface $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = '';

    /** Десятичные строки (bcmath); для unified используется $rateAny. */
    #[ORM\Column(type: 'string', length: 32)]
    private string $rateInput = '0.000000';

    #[ORM\Column(type: 'string', length: 32)]
    private string $rateOutput = '0.000000';

    #[ORM\Column(type: 'string', length: 32)]
    private string $rateCacheRead = '0.000000';

    #[ORM\Column(type: 'string', length: 32)]
    private string $rateCacheWrite = '0.000000';

    #[ORM\Column(type: 'string', length: 32)]
    private string $rateAny = '0.000000';

    /**
     * Поставщик строки цен в контексте тарификации: id модели-владельца.
     * Используется для пересчёта после reprice-замены.
     *
     * @param string $modelId строка-идентификатор модели в форме "provider/code"
     */
    public function __construct(Model $model, \DateTimeInterface $validFrom)
    {
        $this->model = $model;
        $this->validFrom = $validFrom;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getValidFrom(): \DateTimeInterface
    {
        return $this->validFrom;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getPricingUnit(): PricingUnit
    {
        return PricingUnit::fromString($this->unit);
    }

    public function rate(string $kind): string
    {
        return match ($kind) {
            'input' => $this->rateInput,
            'output' => $this->rateOutput,
            'cache_read' => $this->rateCacheRead,
            'cache_write' => $this->rateCacheWrite,
            'any' => $this->rateAny,
            default => throw new \DomainException("Неизвестный вид ставки: {$kind}"),
        };
    }

    public function setRates(string $unit, array $rates): void
    {
        $unit = PricingUnit::fromString($unit);
        $this->unit = $unit->value;
        $r = array_fill_keys(['input', 'output', 'cache_read', 'cache_write', 'any'], '0.000000');
        foreach ($rates as $k => $v) {
            $r[$k] = $v;
        }
        $this->rateInput = $r['input'];
        $this->rateOutput = $r['output'];
        $this->rateCacheRead = $r['cache_read'];
        $this->rateCacheWrite = $r['cache_write'];
        $this->rateAny = $r['any'];
    }

    public function setCurrency(string $c): void
    {
        $this->currency = $c;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getRateInput(): string
    {
        return $this->rateInput;
    }

    public function getRateOutput(): string
    {
        return $this->rateOutput;
    }

    public function getRateCacheRead(): string
    {
        return $this->rateCacheRead;
    }

    public function getRateCacheWrite(): string
    {
        return $this->rateCacheWrite;
    }

    public function getRateAny(): string
    {
        return $this->rateAny;
    }
}
