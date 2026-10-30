<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\ValueObject\Price;
use App\Repository\PriceLineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A price line for a model effective from a specific instant (UTC).
 * Multiple lines per model are allowed; billing picks the one with the
 * greatest validFrom <= event ts.
 *
 * The concrete rates live in the serialized Price VObject on the
 * `price_serialized` column (kept so we never lose precision on round
 * trip).
 */
#[ORM\Entity(repositoryClass: PriceLineRepository::class)]
#[ORM\Table(name: 'price_lines')]
#[ORM\UniqueConstraint(name: 'uq_price_lines_key', columns: ['provider_code', 'model_code', 'valid_from'])]
class PriceLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 40)]
    private string $unit;

    /** @var array<string,mixed> JSON-serialized Price VObject */
    #[ORM\Column(type: 'json')]
    private array $priceSerialized;

    private ?Price $price = null;

    public function __construct(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom, Price $price)
    {
        $this->providerCode = $providerCode;
        $this->modelCode = $modelCode;
        $this->validFrom = $validFrom;
        $this->currency = "USD";
        $this->unit = $price->unit();
        $this->priceSerialized = $price->serialize();
        $this->price = $price;
    }

    public function price(): Price
    {
        if ($this->price === null) {
            $this->price = Price::deserialize($this->priceSerialized);
        }
        return $this->price;
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

    public function setPrice(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom, Price $price): void
    {
        $this->providerCode = $providerCode;
        $this->modelCode = $modelCode;
        $this->validFrom = $validFrom;
        $this->currency = "USD";
        $this->unit = $price->unit();
        $this->priceSerialized = $price->serialize();
        $this->price = $price;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function unit(): string
    {
        return $this->unit;
    }
}
