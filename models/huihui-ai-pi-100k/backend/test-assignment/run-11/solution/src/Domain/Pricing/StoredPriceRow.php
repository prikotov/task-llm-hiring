<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Billing\Cost;
use App\Domain\Billing\TokenMix;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка ценового справочника модели, действующая с `validFrom`
 * (включительно) до следующей строки (или бесконечности).
 *
 * Естественный ключ — (provider, model, valid_from) (TASK.md: «строки
 * цен — по `(provider, model, valid_from)`»).
 *
 * Схема тарификации (`unit`) и ставки (`rates`) хранят в JSON-объекте
 * — при добавлении нового вида `unit` не требуется миграция.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_key', columns: ['provider', 'model', 'valid_from'])]
class StoredPriceRow
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $model = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 40)]
    public string $validFrom = '';

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    public ?string $currency = null;

    #[ORM\Column(type: 'json')]
    public array $payload = [];

    public static function create(
        string $provider,
        string $model,
        \DateTimeImmutable $validFrom,
        ?string $currency,
        BillingUnit $unit,
        array $rates,
    ): self {
        $row = new self();
        self::applyTo($row, $provider, $model, $validFrom, $currency, $unit, $rates);

        return $row;
    }

    public static function applyTo(
        StoredPriceRow $row,
        string $provider,
        string $model,
        \DateTimeImmutable $validFrom,
        ?string $currency,
        BillingUnit $unit,
        array $rates,
    ): self {
        $row->provider = $provider;
        $row->model = $model;
        $row->validFrom = self::isoDate($validFrom);
        $row->currency = $currency;
        $row->payload = [
            'unit' => $unit->value,
            'rates' => array_map(
                static fn (Cost $c): string => $c->toSixDecimalsTruncated(),
                $rates,
            ),
        ];

        return $row;
    }

    private static function isoDate(\DateTimeImmutable $d): string
    {
        return $d->format('Y-m-d\TH:i:s\\Z');
    }

    public function validFromDateTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->validFrom);
    }

    public function toDomain(): PriceRow
    {
        $unit = BillingUnit::tryFrom((string) ($this->payload['unit'] ?? ''))
            ?? throw new \LogicException(
                "unknown billing unit \"{$this->payload['unit']}\""
            );

        $rates = [];
        foreach ((array) ($this->payload['rates'] ?? []) as $key => $value) {
            $rates[(string) $key] = Cost::make((string) $value);
        }

        return new PriceRow(
            providerCode: $this->provider,
            modelCode: $this->model,
            validFrom: $this->validFromDateTime(),
            currency: (string) ($this->currency ?? 'USD'),
            unit: $unit,
            rates: $rates,
        );
    }
}
