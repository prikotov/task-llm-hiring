<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Decimal;
use App\Domain\Unit;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * Строка цен: действует для модели с valid_from (включительно)
 * до следующей строки той же модели. Значения — десятичные строки.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows', columns: ['provider_id', 'model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    private Model $model;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 3, options: ['default' => 'USD'])]
    private string $currency = 'USD';

    #[ORM\Column(type: Types::STRING, length: 32, enumType: Unit::class)]
    private Unit $unit;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $inputRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $outputRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $cacheReadRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $cacheWriteRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $anyRate = null;

    /**
     * @param array<string, string|null> $rates ключи: input, output, cache_read, cache_write | any
     */
    public function __construct(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        Unit $unit,
        array $rates,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->applyUnitAndRates($unit, $rates);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
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

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function getInputRate(): ?string
    {
        return $this->inputRate;
    }

    public function getOutputRate(): ?string
    {
        return $this->outputRate;
    }

    public function getCacheReadRate(): ?string
    {
        return $this->cacheReadRate;
    }

    public function getCacheWriteRate(): ?string
    {
        return $this->cacheWriteRate;
    }

    public function getAnyRate(): ?string
    {
        return $this->anyRate;
    }

    /**
     * Ставка соответствующей схемы; у схемы per_1m_tokens все четыре обязанны,
     * у unified — только any.
     */
    public function rateFor(string $kind): Decimal
    {
        return match ($kind) {
            'input' => Decimal::required($this->inputRate, 'input'),
            'output' => Decimal::required($this->outputRate, 'output'),
            'cache_read' => Decimal::required($this->cacheReadRate, 'cache_read'),
            'cache_write' => Decimal::required($this->cacheWriteRate, 'cache_write'),
            'any' => Decimal::required($this->anyRate, 'any'),
            default => throw new DomainException(sprintf('Неизвестный вид токенов: "%s".', $kind)),
        };
    }

    /**
     * @param array<string, string|null> $rates
     */
    public function applyUnitAndRates(Unit $unit, array $rates): void
    {
        if ($unit === Unit::PerToken) {
            $this->inputRate = self::requiredRate($rates, 'input', $unit);
            $this->outputRate = self::requiredRate($rates, 'output', $unit);
            $this->cacheReadRate = self::requiredRate($rates, 'cache_read', $unit);
            $this->cacheWriteRate = self::requiredRate($rates, 'cache_write', $unit);
            $this->anyRate = null;
        } else {
            $this->anyRate = self::requiredRate($rates, 'any', $unit);
            $this->inputRate = null;
            $this->outputRate = null;
            $this->cacheReadRate = null;
            $this->cacheWriteRate = null;
        }

        $this->unit = $unit;
    }

    /**
     * @param array<string, string|null> $rates
     */
    private static function requiredRate(array $rates, string $key, Unit $unit): string
    {
        $value = $rates[$key] ?? null;
        if ($value === null || $value === '') {
            throw new DomainException(sprintf('Схема "%s" требует ставку "%s".', $unit->value, $key));
        }

        return (string) $value;
    }
}
