<?php

declare(strict_types=1);

namespace App\Domain\Price;

use App\Domain\Model\Model;
use App\Domain\Provider\Provider;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели: тариф в силе с `validFrom`.
 *
 * Натуральный ключ: (provider, model, valid_from).
 * Для одного (provider, model) может быть несколько таких строк —
 * тарифицируется той, у которой наибольшее valid_from <= ts события.
 *
 * rates — JSON объект, значения — десятичные строки (6 знаков).
 */
#[ORM\Entity]
#[ORM\Table(
    name: 'prices',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_price', columns: ['provider', 'model', 'valid_from']),
    ],
)]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'provider_code', referencedColumnName: 'code', onDelete: 'CASCADE')]
    private Provider $provider;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Model $model;

    /** ISO-8601 UTC moment, с которого строка действует (включительно). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    /** Зарезервировано на будущее, везде USD. */
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string')]
    private string $unit;

    /** @var array<string,string> ключи зависят от unit */
    #[ORM\Column(type: 'json')]
    private array $rates;

    public function __construct(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        RateUnit $unit,
        array $rates,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit->value;
        $this->rates = $rates;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function unit(): RateUnit
    {
        return RateUnit::from($this->unit);
    }

    /** @param string $key один из ожидаемых unit rate keys */
    public function rate(string $key): string
    {
        return $this->rates[$key];
    }

    /** @return array<string, string> */
    public function rates(): array
    {
        return $this->rates;
    }

    /** Для reprice: обновить rates + currency (idempotent). */
    public function setRates(string $currency, RateUnit $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit->value;
        $this->rates = $rates;
    }
}
