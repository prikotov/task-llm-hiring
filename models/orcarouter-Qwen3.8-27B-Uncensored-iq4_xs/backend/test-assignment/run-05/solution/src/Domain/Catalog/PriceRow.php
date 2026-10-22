<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Support\Decimal;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен: цена модели с момента valid_from (включительно).
 * Ключ записи — (provider, model, valid_from).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_provider_model_from', columns: ['provider', 'model', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_lookup', columns: ['provider', 'model', 'valid_from'])]
class PriceRow
{
    public const TOKENS_PER_UNIT = 1_000_000;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $provider = '';

    #[ORM\Column(type: 'string', length: 128)]
    private string $model = '';

    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32, enumType: RateUnit::class)]
    private RateUnit $unit;

    /** JSON-строка: ключи вида → десятичная строка с 6 знаками. */
    #[ORM\Column(name: 'rates_json', type: 'text')]
    private string $ratesJson = '{}';

    private ?array $rates = null;

    public function __construct()
    {
        $this->validFrom = new \DateTimeImmutable('1970-01-01T00:00:00+00:00');
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function model(): string
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

    public function unit(): RateUnit
    {
        return $this->unit;
    }

    /** @return array<string, string> нормализованные ставки (6 знаков) */
    public function rates(): array
    {
        return $this->rates ??= json_decode($this->ratesJson, true);
    }

    /** Ставка по ключу вида ("input"/"output"/.../"any") как десятичная строка. */
    public function rate(string $kind): string
    {
        return $this->rates()["$kind"];
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function setValidFrom(\DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function set(string $currency, RateUnit $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = null;
        $this->ratesJson = json_encode($this->normalizeRates($unit, $rates), JSON_THROW_ON_ERROR);
    }

    /** @param array<string, mixed> $rates */
    private function normalizeRates(RateUnit $unit, array $rates): array
    {
        $normalized = [];
        foreach ($unit->rateKeys() as $kind) {
            if (!array_key_exists($kind, $rates)) {
                throw new \DomainException("rates: отсутствует ставка для вида \"$kind\" (схема {$unit->value})");
            }
            $normalized[$kind] = Decimal::normalize6(Decimal::validate($kind, $rates[$kind]));
        }

        return $normalized;
    }
}
