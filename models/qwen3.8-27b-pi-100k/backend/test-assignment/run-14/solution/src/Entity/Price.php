<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен (model, valid_from) со ставками.
 * Natural key: (provider, model, valid_from).
 *
 * Stawki хранятся как строки decimal (bcmath-friendly) — float запрещён.
 * `unit` определяет схему — 'per_1m_tokens' | 'per_1m_tokens_unified'.
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'ux_prices_provider_model_from', columns: ['provider_code', 'model_code', 'valid_from'])]
class Price
{
    public const CURRENCY_USD = 'USD';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    // Natural FK (no ORM association — see notes)
    #[ORM\Column(type: 'string', length: 64)]
    private string $providerCode = '';

    #[ORM\Column(type: 'string', length: 64)]
    private string $modelCode = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = self::CURRENCY_USD;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = 'per_1m_tokens';

    // Ставки — decimal строки. Для per_1m_tokens: input/output/cache_read/cache_write.
    // Для per_1m_tokens_unified: any.
    #[ORM\Column(type: 'string', length: 28, nullable: true)]
    private string|null $rateInput = null;

    #[ORM\Column(type: 'string', length: 28, nullable: true)]
    private string|null $rateOutput = null;

    #[ORM\Column(type: 'string', length: 28, nullable: true)]
    private string|null $rateCacheRead = null;

    #[ORM\Column(type: 'string', length: 28, nullable: true)]
    private string|null $rateCacheWrite = null;

    #[ORM\Column(type: 'string', length: 28, nullable: true)]
    private string|null $rateAny = null;

    public function id(): int
    {
        return $this->id;
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function modelCode(): string
    {
        return $this->modelCode;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function label(): string
    {
        return sprintf('%s/%s', $this->providerCode, $this->modelCode);
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function unit(): string
    {
        return $this->unit;
    }

    public function rateInput(): ?string
    {
        return $this->rateInput;
    }

    public function rateOutput(): ?string
    {
        return $this->rateOutput;
    }

    public function rateCacheRead(): ?string
    {
        return $this->rateCacheRead;
    }

    public function rateCacheWrite(): ?string
    {
        return $this->rateCacheWrite;
    }

    public function rateAny(): ?string
    {
        return $this->rateAny;
    }

    /**
     * Обновляет ставку/схему при повторном импорте (upsert).
     * Не изменяет id / valid_from / provider / model.
     */
    public function replaceRates(string $currency, string $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rateInput = $rates['input'] ?? null;
        $this->rateOutput = $rates['output'] ?? null;
        $this->rateCacheRead = $rates['cache_read'] ?? null;
        $this->rateCacheWrite = $rates['cache_write'] ?? null;
        $this->rateAny = $rates['any'] ?? null;
    }

    public static function create(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ): self {
        $row = new self();
        $row->providerCode = $providerCode;
        $row->modelCode = $modelCode;
        $row->validFrom = $validFrom;
        $row->currency = $currency;
        $row->unit = $unit;

        $row->rateInput = $rates['input'] ?? null;
        $row->rateOutput = $rates['output'] ?? null;
        $row->rateCacheRead = $rates['cache_read'] ?? null;
        $row->rateCacheWrite = $rates['cache_write'] ?? null;
        $row->rateAny = $rates['any'] ?? null;

        return $row;
    }

    /**
     * Возвращает PricedModel (доменная сущность) для расчёта.
     */
    public function pricedModel(): \App\Domain\Pricing\PricedModel
    {
        $scheme = \App\Domain\Pricing\PricingScheme::parse($this->unit);
        $rates = array_filter([
            'input' => $this->rateInput,
            'output' => $this->rateOutput,
            'cache_read' => $this->rateCacheRead,
            'cache_write' => $this->rateCacheWrite,
            'any' => $this->rateAny,
        ], static fn ($v) => $v !== null);

        return new \App\Domain\Pricing\PricedModel($scheme, $rates);
    }
}
