<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен.
 *
 * @ORM\Entity
 * @ORM\Table(name: "price_history")
 */
final class PriceRow
{
    /**
     * @ORM\Id
     * @ORM\Column(type: "integer")
     */
    private int $id;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $provider;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $model;

    /**
     * @ORM\Column(type: "datetime")
     */
    private \DateTimeImmutable $validFrom;

    /**
     * @ORM\Column(type: "string", length: 8)
     */
    private string $currency;

    /**
     * @ORM\Column(type: "string", length: 64)
     */
    private string $unit;

    /**
     * @ORM\Column(type: "string", length: 64) nullable=true
     */
    private ?string $rateInput = null;

    /**
     * @ORM\Column(type: "string", length: 64) nullable=true
     */
    private ?string $rateOutput = null;

    /**
     * @ORM\Column(type: "string", length: 64) nullable=true
     */
    private ?string $rateCacheRead = null;

    /**
     * @ORM\Column(type: "string", length: 64) nullable=true
     */
    private ?string $rateCacheWrite = null;

    /**
     * @ORM\Column(type: "string", length: 64) nullable=true
     */
    private ?string $rateAny = null;

    public function __construct()
    {
        $this->id = 0;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function setValidFrom(\DateTimeImmutable $validFrom): void
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
     * @param array<string, string> $rates
     */
    public function setRates(array $rates): void
    {
        $this->rateInput = isset($rates['input']) ? (string) $rates['input'] : null;
        $this->rateOutput = isset($rates['output']) ? (string) $rates['output'] : null;
        $this->rateCacheRead = isset($rates['cache_read']) ? (string) $rates['cache_read'] : null;
        $this->rateCacheWrite = isset($rates['cache_write']) ? (string) $rates['cache_write'] : null;
        $this->rateAny = isset($rates['any']) ? (string) $rates['any'] : null;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getModel(): string
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

    public function getRateInput(): ?string
    {
        return $this->rateInput;
    }

    public function getRateOutput(): ?string
    {
        return $this->rateOutput;
    }

    public function getRateCacheRead(): ?string
    {
        return $this->rateCacheRead;
    }

    public function getRateCacheWrite(): ?string
    {
        return $this->rateCacheWrite;
    }

    public function getRateAny(): ?string
    {
        return $this->rateAny;
    }

    /**
     * @return array<string, string>
     */
    public function getRates(): array
    {
        $r = [];
        if ($this->rateInput !== null) {
            $r['input'] = $this->rateInput;
        }
        if ($this->rateOutput !== null) {
            $r['output'] = $this->rateOutput;
        }
        if ($this->rateCacheRead !== null) {
            $r['cache_read'] = $this->rateCacheRead;
        }
        if ($this->rateCacheWrite !== null) {
            $r['cache_write'] = $this->rateCacheWrite;
        }
        if ($this->rateAny !== null) {
            $r['any'] = $this->rateAny;
        }
        return $r;
    }
}
