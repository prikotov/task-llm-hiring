<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Shared\Decimal;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен модели: действует с `validFrom` (включительно)
 * до наступления более поздней строки той же модели.
 *
 * Естественный ключ — (provider, model, valid_from), повторная загрузка
 * тех же сумм является идемпотентным upsert'ом.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows', columns: ['provider', 'model', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\TypedIdentifier]
    #[ORM\Column(type: 'bigint')]
    private null|int $id = null;

    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3, options: ['default' => 'USD'])]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32, enumType: PriceUnit::class)]
    private PriceUnit $unit;

    #[ORM\Column(name: 'input_rate', type: 'decimal', precision: 20, scale: 10, options: ['default' => null])]
    private null|string $inputRate = null;

    #[ORM\Column(name: 'output_rate', type: 'decimal', precision: 20, scale: 10, options: ['default' => null])]
    private null|string $outputRate = null;

    #[ORM\Column(name: 'cache_read_rate', type: 'decimal', precision: 20, scale: 10, options: ['default' => null])]
    private null|string $cacheReadRate = null;

    #[ORM\Column(name: 'cache_write_rate', type: 'decimal', precision: 20, scale: 10, options: ['default' => null])]
    private null|string $cacheWriteRate = null;

    #[ORM\Column(name: 'any_rate', type: 'decimal', precision: 20, scale: 10, options: ['default' => null])]
    private null|string $anyRate = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    private Model $model;

    public function __construct(
        Provider $provider,
        Model $model,
        DateTimeImmutable $validFrom,
        string $currency,
        PriceUnit $unit,
        null|string $inputRate,
        null|string $outputRate,
        null|string $cacheReadRate,
        null|string $cacheWriteRate,
        null|string $anyRate,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->inputRate = $inputRate !== null ? Decimal::normalize($inputRate) : null;
        $this->outputRate = $outputRate !== null ? Decimal::normalize($outputRate) : null;
        $this->cacheReadRate = $cacheReadRate !== null ? Decimal::normalize($cacheReadRate) : null;
        $this->cacheWriteRate = $cacheWriteRate !== null ? Decimal::normalize($cacheWriteRate) : null;
        $this->anyRate = $anyRate !== null ? Decimal::normalize($anyRate) : null;
    }

    /**
     * Замена тарифа строки (upsert / ретро-коррекция).
     *
     * @param array{input?: null|string, output?: null|string, cache_read?: null|string, cache_write?: null|string, any?: null|string} $rates
     */
    public function replaceRates(
        string $currency,
        PriceUnit $unit,
        array $rates,
    ): void {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->inputRate = isset($rates['input']) ? Decimal::normalize((string) $rates['input']) : null;
        $this->outputRate = isset($rates['output']) ? Decimal::normalize((string) $rates['output']) : null;
        $this->cacheReadRate = isset($rates['cache_read']) ? Decimal::normalize((string) $rates['cache_read']) : null;
        $this->cacheWriteRate = isset($rates['cache_write']) ? Decimal::normalize((string) $rates['cache_write']) : null;
        $this->anyRate = isset($rates['any']) ? Decimal::normalize((string) $rates['any']) : null;
    }

    public function getId(): null|int
    {
        return $this->id;
    }

    public function getValidFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUnit(): PriceUnit
    {
        return $this->unit;
    }

    public function getInputRate(): null|string
    {
        return $this->inputRate;
    }

    public function getOutputRate(): null|string
    {
        return $this->outputRate;
    }

    public function getCacheReadRate(): null|string
    {
        return $this->cacheReadRate;
    }

    public function getCacheWriteRate(): null|string
    {
        return $this->cacheWriteRate;
    }

    public function getAnyRate(): null|string
    {
        return $this->anyRate;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}
