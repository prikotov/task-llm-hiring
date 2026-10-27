<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Money;
use App\Domain\PriceUnit;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены модели. Естественный ключ — (provider, model, validFromEpoch).
 */
#[ORM\Entity]
#[ORM\Table(name: 'billing_price_row')]
#[ORM\UniqueConstraint(name: 'uq_billing_price_row', columns: ['provider', 'model', 'valid_from_epoch'])]
#[ORM\Index(name: 'idx_billing_price_row_lookup', columns: ['provider', 'model', 'valid_from_epoch'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $provider;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $model;

    #[ORM\Column(type: Types::INTEGER)]
    private int $validFromEpoch;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $unit;

    #[ORM\Column(type: Types::JSON)]
    private array $rates;

    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        string $provider,
        string $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFromEpoch = (clone $validFrom)->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = PriceUnit::normalizeRates($unit, $rates);
    }

    public function id(): ?int { return $this->id; }
    public function providerValue(): string { return $this->provider; }
    public function modelValue(): string { return $this->model; }
    public function validFromEpochValue(): int { return $this->validFromEpoch; }
    public function currencyValue(): string { return $this->currency; }
    public function unitValue(): string { return $this->unit; }

    /** @return array<string, string> */
    public function ratesValue(): array { return $this->rates; }

    /**
     * @param array<string, string> $rates
     */
    public function update(string $currency, string $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = PriceUnit::normalizeRates($unit, $rates);
    }

    public static function rateKey(string $provider, string $model, int $validFromEpoch): string
    {
        return implode('|', [$provider, $model, (string) $validFromEpoch]);
    }
}
