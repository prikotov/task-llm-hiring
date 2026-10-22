<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    /** Ссылка на catalog_models.id (уникальность пары (provider, model, valid_from) — через provider у модели). */
    #[ORM\Column(name: 'model_id', type: 'integer')]
    public int $model = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    /** зарезервировано; на практике только "USD" */
    #[ORM\Column(type: 'string', length: 3)]
    public string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    public string $unit = '';

    /**
     * JSON-объект десятичных строк, ключи — согласно unit,
     * например {"input":"3.000000","output":"12.000000",...} или {"any":"2.400000"}.
     */
    #[ORM\Column(type: 'text')]
    public string $rates = '{}';

    /**
     * @return array<string, string>
     */
    public function rates(): array
    {
        $decoded = json_decode($this->rates, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    public function pricingUnit(): PricingUnit
    {
        return PricingUnit::from($this->unit);
    }
}
