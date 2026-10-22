<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine\Entity;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\RateCard;
use App\Billing\Domain\Model\TariffUnit;
use App\Billing\Domain\Money\Money;
use App\Billing\Domain\Pricing\PriceRow;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен. Ключ уникальности — (model, valid_from);
 * ставки хранятся JSON-объектом из decimal-строк.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_row')]
#[ORM\UniqueConstraint(name: 'uniq_price_row_model_valid_from', columns: ['model_id', 'valid_from'])]
final class PriceRowRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ModelRecord::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private ModelRecord $model;

    #[ORM\Column(name: 'valid_from', type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(name: 'currency', type: Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(name: 'unit', type: Types::STRING, length: 32)]
    private string $unit;

    /**
     * @var array<string, string> decimal-строки, никогда не float
     */
    #[ORM\Column(name: 'rates', type: Types::JSON)]
    private array $rates;

    public static function fromDomain(PriceRow $row, ModelRecord $model): self
    {
        $record = new self();
        $record->model = $model;
        $record->apply($row);

        return $record;
    }

    public function apply(PriceRow $row): void
    {
        $this->validFrom = $row->validFrom;
        $this->currency = $row->currency;
        $this->unit = $row->rateCard->unit()->value;

        $rates = [];
        foreach ($row->rateCard->rates() as $key => $rate) {
            $rates[$key] = $rate->roundHalfUp(Money::MONEY_SCALE)->toString();
        }
        $this->rates = $rates;
    }

    public function toDomain(): PriceRow
    {
        $rates = [];
        foreach ($this->rates as $key => $value) {
            $rates[$key] = Money::fromString((string) $value);
        }

        return new PriceRow(
            $this->id,
            new ModelReference($this->model->provider()->code(), $this->model->code()),
            $this->validFrom,
            $this->currency,
            RateCard::of(TariffUnit::from($this->unit), $rates),
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function model(): ModelRecord
    {
        return $this->model;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }
}
