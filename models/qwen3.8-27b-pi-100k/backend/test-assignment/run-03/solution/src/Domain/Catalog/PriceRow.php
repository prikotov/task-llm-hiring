<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены: тариф модели, действующий с valid_from.
 *
 * Естественный ключ — (provider, model, valid_from), что при разбивке
 * на таблицы эквивалентно уникальности (model, valid_from).
 * Заменяется на месте (upsert) — id не меняется, по нему события
 * помнят, какой строкой они тарифицированы (основа app:billing:reprice).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Model $model;

    /** Момент действия тарифа, UTC (включительно). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    /** Валюта; пока всегда USD, поле зарезервировано на будущее. */
    #[ORM\Column(type: 'string', length: 8)]
    private string $currency;

    #[ORM\Column(type: 'string', enumType: PricingUnit::class, length: 32)]
    private PricingUnit $unit;

    /** Ставки: ключ — вид токенов (input/output/cache_read/cache_write или any), значение — десятичная строка. */
    #[ORM\Column(type: 'json')]
    private array $rates = [];

    public function __construct(
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        PricingUnit $unit,
        array $rates,
    ) {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function provider(): Provider
    {
        return $this->model->provider();
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

    /** @return array<string, string> ставки */
    public function rates(): array
    {
        return $this->rates;
    }

    /**
     * Замена условий строки (коррекция тарифа): id сохраняется,
     * события, ссылающиеся на строку, остаются связанными.
     */
    public function replaceTerms(string $currency, PricingUnit $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }
}
