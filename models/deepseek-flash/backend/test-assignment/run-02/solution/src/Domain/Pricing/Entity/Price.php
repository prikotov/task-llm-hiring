<?php

declare(strict_types=1);

namespace App\Domain\Pricing\Entity;

use App\Domain\Provider\Entity\Provider;
use App\Domain\Pricing\ValueObject\PricingUnit;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uniq_prices_model_valid_from', columns: ['provider_model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_prices_model_valid_from', columns: ['provider_model_id', 'valid_from'])]
final class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProviderModel::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'provider_model_id', nullable: false, onDelete: 'CASCADE')]
    private ProviderModel $providerModel;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', nullable: false, onDelete: 'CASCADE')]
    private Provider $provider;

    #[ORM\Column(name: 'valid_from', type: 'datetimetz_immutable')]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(name: 'pricing_unit', type: 'string', length: 32, enumType: PricingUnit::class)]
    private PricingUnit $unit;

    /** @var array<string, string> */
    #[ORM\Column(type: 'json')]
    private array $rates;

    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        ProviderModel $providerModel,
        DateTimeImmutable $validFrom,
        string $currency,
        PricingUnit $unit,
        array $rates,
    ) {
        $this->providerModel = $providerModel;
        $this->provider = $providerModel->provider();
        $this->validFrom = $validFrom->setTimezone(new \DateTimeZone('UTC'));
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function providerModel(): ProviderModel
    {
        return $this->providerModel;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function validFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function unit(): PricingUnit
    {
        return $this->unit;
    }

    /** @return array<string, string> */
    public function rates(): array
    {
        return $this->rates;
    }

    /**
     * Обновляет содержимое строки цен. valid_from и модель не меняются,
     * потому что они являются ключом строки.
     *
     * @param array<string, string> $rates
     */
    public function replaceContents(string $currency, PricingUnit $unit, array $rates): bool
    {
        $changed = $this->currency !== $currency
            || $this->unit !== $unit
            || $this->rates !== $rates;

        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;

        return $changed;
    }
}
