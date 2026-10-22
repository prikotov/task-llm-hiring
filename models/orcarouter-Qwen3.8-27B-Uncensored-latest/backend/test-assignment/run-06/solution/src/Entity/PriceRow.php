<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен: (provider, model, valid_from) — естественный ключ.
 *
 * @method \Doctrine\ORM\QueryBuilder|PriceRowRepository newQuery()
 * @method bool isNull(string $field)
 * @method bool isNotNull(string $field)
 * @method static mixed createQueryBuilder()
 */
#[ORM\Entity(repositoryClass: \App\Repository\PriceRowRepository::class)]
#[ORM\Table(name: 'price_rows', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_price_provider_model_from', columns: ['provider', 'model', 'valid_from'])])]
final class PriceRow
{
    /** per_1m_tokens | per_1m_tokens_unified */
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'string', length: 128)]
    public string $provider;

    #[ORM\Column(type: 'string', length: 128)]
    public string $model;

    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    public string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    public string $unit;

    /**
     * Ставки в виде JSON-объекта «вид → десятичная строка».
     * Строка, а не число/float: деньги — только точная децималка.
     *
     * @var string
     */
    #[ORM\Column(name: 'rates_json', type: 'text')]
    public string $ratesJson;

    /** @param array<string, string> $rates */
    public function __construct(string $provider, string $model, \DateTimeImmutable $validFrom, string $currency, string $unit, array $rates)
    {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->ratesJson = json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /** @return array<string, string> */
    public function rates(): array
    {
        $decoded = json_decode($this->ratesJson, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException(sprintf('Повреждённые ставки строки цен id=%d', $this->id));
        }

        return $decoded;
    }
}
