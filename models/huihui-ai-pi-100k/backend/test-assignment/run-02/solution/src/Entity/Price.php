<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\PricingUnit;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Строка истории цен: цена модели, действующая с момента valid_from
 * (включительно, до появления следующей более поздней строки).
 *
 * Естественный ключ — (model, valid_from).
 * Ставки — точные десятичные числа, значения в JSON хранятся строками
 * (float для денег не используется).
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices')]
final class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', nullable: false, onDelete: 'CASCADE')]
    public Model $model;

    /** Момент начала действия цены (UTC, включительно). */
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(length: 8)]
    public string $currency = 'USD';

    /** Схема тарификации — строка PricingUnit (per_1m_tokens / per_1m_tokens_unified). */
    #[ORM\Column(length: 32)]
    public string $unit = PricingUnit::PerMillionPerKind->value;

    /**
     * Ставки в видах токенов (ключи — входной формат:
     * input/output/cache_read/cache_write или any), значения —
     * десятичные строки.
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: Types::JSON)]
    public array $rates = [];

    public function pricingUnit(): PricingUnit
    {
        return PricingUnit::from($this->unit);
    }

    /**
     * Ставки строки (ключи соответствуют схеме $this->unit),
     * десятичные строки — готовы к точной арифметике.
     *
     * @return array<string, string>
     */
    public function ratesByKind(): array
    {
        $rates = [];
        foreach ($this->rates as $key => $value) {
            $rates[(string) $key] = is_string($value) ? $value : (string) $value;
        }

        return $rates;
    }

    /**
     * Заменяет схему и ставки строки (chystая upsert-семантика —
     * весь набор ставок перезаписывается целиком).
     *
     * @param array<string, string> $rates
     */
    public function applyRates(string $unit, array $rates): void
    {
        $this->unit = $unit;
        $this->rates = array_map(
            static fn ($v): string => is_string($v) ? $v : (string) $v,
            $rates,
        );
    }
}
