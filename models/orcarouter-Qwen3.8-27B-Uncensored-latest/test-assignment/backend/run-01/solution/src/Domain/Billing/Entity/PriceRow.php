<?php

declare(strict_types=1);

namespace App\Domain\Billing\Entity;

use App\Domain\Billing\Enum\PriceUnit;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели с определённого момента времени (valid_from, включительно).
 * Один (provider, model) может иметь несколько строк с разными valid_from —
 * это история изменения цен.
 *
 * Тарифицируется самая поздняя строка с valid_from <= ts события.
 *
 * Ставки хранятся как строки (десятичная арифметика через bcmath, без float).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_provider_model_from', columns: ['provider', 'model', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Column(type: 'string', length: 64)]
    public string $model = '';

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3, options: ['default' => 'USD'])]
    public string $currency = 'USD';

    /** @var string одно из PriceUnit::cases() */
    #[ORM\Column(type: 'string', length: 40)]
    public string $unit = PriceUnit::Per1mTokens->value;

    // Схема per_1m_tokens: ставка за 1M токенов конкретного вида.
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $rateInput = null;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $rateOutput = null;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $rateCacheRead = null;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $rateCacheWrite = null;

    // Схема per_1m_tokens_unified: единая ставка за 1M токенов любого вида.
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $rateAny = null;

    public function __construct(string $provider, string $model, \DateTimeImmutable $validFrom, string $unit)
    {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->unit = $unit;
    }

    public function unitEnum(): PriceUnit
    {
        return PriceUnit::fromString($this->unit);
    }

    /**
     * Применяет набор ставок, валидируя его по схеме единицы измерения.
     *
     * @param array{unit: string, currency?: string, rates: array<string, string>} $data
     */
    public function apply(string $currency, string $unit, array $rates): void
    {
        $this->unit = $unit;
        $this->currency = $currency;
        $unitEnum = PriceUnit::fromString($unit);

        $this->resetRates();

        switch ($unitEnum) {
            case PriceUnit::Per1mTokens:
                $this->rateInput = self::requireRate('input', $rates);
                $this->rateOutput = self::requireRate('output', $rates);
                $this->rateCacheRead = self::requireRate('cache_read', $rates);
                $this->rateCacheWrite = self::requireRate('cache_write', $rates);
                break;
            case PriceUnit::Per1mTokensUnified:
                $this->rateAny = self::requireRate('any', $rates);
                break;
        }
    }

    private function resetRates(): void
    {
        $this->rateInput = null;
        $this->rateOutput = null;
        $this->rateCacheRead = null;
        $this->rateCacheWrite = null;
        $this->rateAny = null;
    }

    private static function requireRate(string $name, array $rates): string
    {
        if (!isset($rates[$name]) || $rates[$name] === null || $rates[$name] === '') {
            throw new \InvalidArgumentException(
                sprintf('rates.%s: ставка обязательна для выбранной схемы тарификации.', $name)
            );
        }
        $value = (string) $rates[$name];
        if (!preg_match('/^\d+(\.\d+)?$/', $value)) {
            throw new \InvalidArgumentException(
                sprintf('rates.%s: ожидалось десятичное число, получено "%s".', $name, $value)
            );
        }
        return $value;
    }
}
