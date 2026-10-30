<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

use App\Shared\Decimal;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен модели, действующая с valid_from (включительно) до следующей строки.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_row')]
#[ORM\UniqueConstraint(name: 'uniq_price_model_valid_from', columns: ['model_id', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 32, enumType: PricingUnit::class)]
    private PricingUnit $unit;

    /** @var array<string, string> */
    #[ORM\Column(type: Types::JSON)]
    private array $rates;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: LlmModel::class)]
        #[ORM\JoinColumn(nullable: false)]
        private LlmModel $model,
        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
        private DateTimeImmutable $validFrom,
        string $currency,
        Rates $rates,
    ) {
        $this->currency = $currency;
        $this->unit = $rates->unit;
        $this->rates = $rates->toArray();
    }

    /** @return bool изменились ли данные */
    public function change(string $currency, Rates $rates): bool
    {
        if ($this->currency === $currency && $this->rates()->equals($rates)) {
            return false;
        }
        $this->currency = $currency;
        $this->unit = $rates->unit;
        $this->rates = $rates->toArray();

        return true;
    }

    public function rates(): Rates
    {
        return Rates::fromArray($this->unit, $this->rates);
    }

    /** Итоговая стоимость события: точный расчёт, оф-пик, единственное округление до 6 знаков HALF_UP. */
    public function costOf(TokenUsage $tokens, DateTimeImmutable $ts): Decimal
    {
        $cost = $this->rates()->exactCost($tokens);
        $offPeak = $this->model->provider()->offPeak();
        if ($offPeak !== null && $offPeak->contains($ts)) {
            $cost = $cost->mul($offPeak->multiplier);
        }

        return $cost->round(6);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function model(): LlmModel
    {
        return $this->model;
    }

    public function validFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }
}
