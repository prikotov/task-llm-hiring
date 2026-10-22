<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Money;
use App\Repository\PriceRowRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Строка цен модели, действующая с момента validFrom (UTC).
 *
 * Натуральный ключ — (model, validFrom) — то есть (provider, model, valid_from).
 * Ставки хранятся десятичными строками (JSON), float не используется.
 */
#[ORM\Entity(repositoryClass: PriceRowRepository::class)]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    #[ORM\Column(name: 'valid_from', type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 8)]
    private string $currency;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $unit;

    /** @var array<string, string> ставки: вид токена -> десятичная строка */
    #[ORM\Column(type: Types::JSON)]
    private array $rates = [];

    public function __construct(string $unit, DateTimeImmutable $validFrom, string $currency, array $rates, Model $model)
    {
        $this->unit = $unit;
        $this->validFrom = $validFrom->setTimezone(new \DateTimeZone('UTC'));
        $this->currency = $currency;
        $this->rates = $rates;
        $this->model = $model;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getProviderCode(): string
    {
        return $this->model->getProviderCode();
    }

    public function getModelCode(): string
    {
        return $this->model->getCode();
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

    /** @return array<string, string> */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /** @param array<string, string> $rates */
    public function setRates(array $rates): void
    {
        $this->rates = $rates;
    }

    /**
     * true, если новая версия строки цен отличается от хранимой по любому полю
     * (сравнение ставок — числовое, б/знаков после точки).
     */
    public function sameContent(string $unit, DateTimeImmutable $validFrom, string $currency, array $rates): bool
    {
        return $this->unit === $unit
            && $this->validFrom->setTimezone(new \DateTimeZone('UTC'))->getTimestamp() === $validFrom->setTimezone(new \DateTimeZone('UTC'))->getTimestamp()
            && $this->currency === $currency
            && $this->ratesEqual($this->rates, $rates);
    }

    /** @internal Только для восстановления сущности из результата SQL-запроса. */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /** @param array<string, string> $a @param array<string, string> $b */
    private function ratesEqual(array $a, array $b): bool
    {
        $keysA = array_keys($a);
        $keysB = array_keys($b);
        sort($keysA);
        sort($keysB);

        if ($keysA !== $keysB) {
            return false;
        }

        foreach ($keysA as $key) {
            if (Money::cmp((string) $a[$key], (string) $b[$key]) !== 0) {
                return false;
            }
        }

        return true;
    }
}
