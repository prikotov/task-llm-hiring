<?php

declare(strict_types=1);

namespace App\Billing\Entity;

use App\Billing\Enum\PricingUnit;
use App\Billing\Import\Dto\PriceRowInput;
use App\Billing\ValueObject\Decimal;
use App\Billing\ValueObject\Price;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены модели: действует с valid_from до появления новой строки той же модели.
 * Upsert по естественному ключу (provider, model, valid_from); замещающий апсерт
 * сохраняет суррогатный id — на него ссылаются рассчитанные события.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_natural', columns: ['provider', 'model', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_lookup', columns: ['provider', 'model', 'valid_from'])]
final class PriceRow
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        #[ORM\Column(type: 'integer')]
        public int $id,

        #[ORM\Column(type: 'string', length: 64)]
        public string $provider,

        #[ORM\Column(type: 'string', length: 64)]
        public string $model,

        #[ORM\Column(type: 'datetime_immutable')]
        public DateTimeImmutable $validFrom,

        #[ORM\Column(type: 'string', length: 8)]
        public string $currency,

        #[ORM\Column(type: 'string', length: 32)]
        public string $unit,

        /** JSON-объект: ключ — вид токена (input|output|cache_read|cache_write|any), значение — decimal-строка. */
        #[ORM\Column(type: 'text')]
        public string $ratesJson,
    ) {
    }

    /** Применяет содержимое строки цен (upsert; суррогатный id не меняется). */
    public function apply(PriceRowInput $input): void
    {
        $this->currency = $input->currency;
        $this->unit = $input->price->unit->value;

        $rates = [];
        foreach ($input->price->rates as $kind => $rate) {
            $rates[$kind] = $rate->value();
        }
        $this->ratesJson = json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function price(): Price
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($this->ratesJson, true, 64, JSON_THROW_ON_ERROR);
        $rates = [];
        foreach ($decoded as $kind => $rate) {
            $rates[(string) $kind] = Decimal::of((string) $rate);
        }

        return new Price(PricingUnit::fromString($this->unit), $rates);
    }
}
