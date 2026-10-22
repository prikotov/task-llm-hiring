<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model_id', 'valid_from']),
])]
final class PriceRow
{
    /** Схема тарификации: цена за 1M токенов отдельно по видам. */
    public const UNIT_PER_TOKEN = 'per_1m_tokens';

    /** Схема тарификации: единая цена за 1M токенов любого вида. */
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(name: 'model_id', type: 'integer')]
    private int $modelId;

    /** Момент, с которого действует строка (включительно), UTC. */
    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    /** JSON-строка ставок (десятичные значения, 6 знаков). */
    #[ORM\Column(type: 'text')]
    private string $rates;

    /** @param array<string,string> $rates */
    public function __construct(
        int $modelId,
        DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ) {
        $this->modelId = $modelId;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = self::encodeRates($rates);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModelId(): int
    {
        return $this->modelId;
    }

    public function getValidFrom(): DateTimeImmutable
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

    public function getRatesJson(): string
    {
        return $this->rates;
    }

    /** @return array<string,string> */
    public function getRates(): array
    {
        $data = json_decode($this->rates, true);
        return is_array($data) ? $data : [];
    }

    /** @param array<string,string> $rates */
    public function setRates(array $rates): void
    {
        $this->rates = self::encodeRates($rates);
    }

    public function setValidFrom(DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    /**
     * Серлиализация ставок как стабильного JSON: ключи в фиксированном порядке,
     * значения — десятичные строки (никаких чисел JSON, чтобы не потерять точность).
     *
     * @param array<string,string> $rates
     */
    private static function encodeRates(array $rates): string
    {
        $ordered = [];
        $order = ['input', 'output', 'cache_read', 'cache_write', 'any'];
        foreach ($order as $key) {
            if (isset($rates[$key])) {
                $ordered[$key] = (string) $rates[$key];
            }
        }
        // сохранение нестандартных ключей (защита от потери данных)
        foreach ($rates as $key => $value) {
            if (!isset($ordered[$key])) {
                $ordered[$key] = (string) $value;
            }
        }
        $json = json_encode($ordered, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $json === false ? '{}' : $json;
    }
}
