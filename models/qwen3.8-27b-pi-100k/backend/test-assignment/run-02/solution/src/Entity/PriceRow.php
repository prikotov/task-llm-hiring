<?php

declare(strict_types=1);

namespace App\Entity;

use App\Billing\Pricing\PriceUnit;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены модели, действующая с момента valid_from (включительно).
 *
 * Естественный ключ — (provider, model, valid_from): у каждой модели
 * (provider/code) уникальная пара (model_id, valid_from).
 *
 * rates хранит только ключи, требуемые схемой ($unit):
 *  - per_1m_tokens: input, output, cache_read, cache_write;
 *  - per_1m_tokens_unified: any.
 * Значения — десятичные строки «за 1 000 000 токенов» (USD).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    /** С этого момента (UTC, включительно) действует строка. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    /** Валюта цены; в задаче всегда USD (поле зарезервировано на будущее). */
    #[ORM\Column(length: 16)]
    private string $currency;

    /** Схема тарификации (значение PriceUnit). */
    #[ORM\Column(length: 32)]
    private string $unit;

    /** @var array<string, string> вид → цена (десятичная строка) */
    #[ORM\Column(type: Types::JSON)]
    private array $rates;

    public function __construct(
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ) {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function unitAsEnum(): PriceUnit
    {
        return PriceUnit::fromWire($this->unit);
    }

    /** @return array<string, string> */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function setTerms(string $currency, string $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    /** Человекочитаемый ключ строки: provider/model@valid_from. */
    public function key(): string
    {
        return sprintf(
            '%s@%s',
            $this->model->label(),
            $this->validFrom->format('Y-m-d\TH:i:s\Z'),
        );
    }
}
