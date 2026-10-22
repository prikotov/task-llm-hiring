<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\Pricing\PriceScheme;
use Doctrine\ORM\Mapping as ORM;

use function json_decode;

/**
 * Строка цен модели, действующая с момента validFrom (включительно),
 * до момента вступления в силу следующей строки той же модели.
 *
 * Значения ставок хранятся десятичными строками — float здесь запрещён.
 */
#[ORM\Entity(repositoryClass: PriceLineRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_price_line', columns: ['provider_id', 'model_id', 'valid_from'])]
class PriceLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'provider_id', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'model_id', nullable: false)]
    private Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    /** Значение PriceScheme (string-enum). */
    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = PriceScheme::PerToken->value;

    /**
     * JSON-связка вида токена → десятичная строка ставки,
     * например {"input":"3.000000", ...} — строки, не числа.
     */
    #[ORM\Column(type: 'text')]
    private string $ratesJson = '{}';

    public function __construct(Provider $provider, Model $model, \DateTimeImmutable $validFrom)
    {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function scheme(): PriceScheme
    {
        return PriceScheme::from($this->unit);
    }

    /** @return array<string, string> ставки как десятичные строки */
    public function rates(): array
    {
        $decoded = json_decode($this->ratesJson, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function setPricingData(string $currency, PriceScheme $scheme, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $scheme->value;

        ksort($rates);
        $this->ratesJson = json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /** Ключевые данные строки (для сравнения при upsert/репайсе). */
    public function dataSignature(): string
    {
        return $this->unit.'|'.$this->ratesJson;
    }

    public function dataSignatureFor(string $unit, array $rates): string
    {
        ksort($rates);
        $json = json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        return $unit.'|'.$json;
    }
}
