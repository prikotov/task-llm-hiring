<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Catalog\PriceSpec;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /**
     * Провайдер определяется через модель (модель принадлежит провайдеру);
     * естественный ключ (provider, model, valid_from) эквивалентен (model_id, valid_from).
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    /**
     * Момент действия цены (UTC, включительно).
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 8)]
    private string $currency;

    /**
     * per_1m_tokens | per_1m_tokens_unified
     */
    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $unit;

    /**
     * Ставки — map вида => десятичная строка, например
     * {"input":"3.000000","output":"12.000000"} или {"any":"2.400000"}.
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $rates = [];

    public function __construct(Model $model, PriceSpec $spec)
    {
        $this->model = $model;
        $this->validFrom = $spec->validFrom;
        $this->currency = $spec->currency;
        $this->unit = $spec->unit->value;
        $this->rates = $spec->rates;
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

    /**
     * @return array<string, string>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * Применяет значения из загруженной строки цен, изменяя состояние,
     * только если они реально отличаются (upsert без лишних UPDATE).
     */
    public function replaceWith(PriceSpec $spec): void
    {
        if ($this->currency !== $spec->currency) {
            $this->currency = $spec->currency;
        }

        if ($this->unit !== $spec->unit->value) {
            $this->unit = $spec->unit->value;
        }

        if (!self::ratesAreEqual($this->rates, $spec->rates)) {
            $this->rates = $spec->rates;
        }
    }

    /**
     * Численное сравнение наборов ставок (ключи и значения),
     * «3.000000» и «3.0» считаются равными.
     *
     * @param array<string, string> $a
     * @param array<string, string> $b
     */
    public static function ratesAreEqual(array $a, array $b): bool
    {
        $aKeys = array_keys($a);
        $bKeys = array_keys($b);
        sort($aKeys);
        sort($bKeys);
        if ($aKeys !== $bKeys) {
            return false;
        }

        foreach ($b as $key => $value) {
            if (bccomp((string) $a[$key], (string) $value, 30) !== 0) {
                return false;
            }
        }

        return true;
    }
}
