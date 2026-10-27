<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use App\Domain\Billing\PriceRowSpec;
use App\Infrastructure\Persistence\Repository\PriceRowRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка справочника цен: ставка на 1M токенов, действует с validFrom.
 *
 * Natural key: (provider, model, valid_from) — упрощённый вариант
 * «(прод-модель, цена, срок)» из TASK.md.
 *
 * Замечание: все ставки хранятся как десятичные строки и применяются
 * только через bcmath (MoneyMath) — никакого float в цепочке расчёта.
 */
#[ORM\Entity(repositoryClass: PriceRowRepository::class)]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uk_price_row', columns: ['provider', 'model', 'valid_from'])]
final class PriceRow
{
    public const UNIT_PER_TOKEN = PriceRowSpec::UNIT_PER_TOKEN;

    public const UNIT_UNIFIED = PriceRowSpec::UNIT_UNIFIED;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    /** Код модели (сопровождается provider вместе образует модель). */
    #[ORM\Column(type: 'string', length: 64)]
    public string $model = '';

    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    public string $currency = 'USD';

    /** СХЕМА ТАРИФИКАЦИИ: per_1m_tokens | per_1m_tokens_unified */
    #[ORM\Column(type: 'string', length: 32)]
    public string $unit = self::UNIT_PER_TOKEN;

    /** Ставка (за 1M) за входные токены; NULL при unified. */
    #[ORM\Column(name: 'rate_input', type: 'string', length: 32, nullable: true)]
    public ?string $rateInput = null;

    /** Ставка (за 1M) за выходные токены; NULL при unified. */
    #[ORM\Column(name: 'rate_output', type: 'string', length: 32, nullable: true)]
    public ?string $rateOutput = null;

    /** Ставка (за 1M) за кэш-чтение; NULL при unified. */
    #[ORM\Column(name: 'rate_cache_read', type: 'string', length: 32, nullable: true)]
    public ?string $rateCacheRead = null;

    /** Ставка (за 1M) за кэш-запись; NULL при unified. */
    #[ORM\Column(name: 'rate_cache_write', type: 'string', length: 32, nullable: true)]
    public ?string $rateCacheWrite = null;

    /** Ставка (за 1M) единая для всех типов токенов; NULL при per-token. */
    #[ORM\Column(name: 'rate_any', type: 'string', length: 32, nullable: true)]
    public ?string $rateAny = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    public function __construct(
        string $provider = '',
        string $model = '',
        ?\DateTimeImmutable $validFrom = null,
        string $currency = 'USD',
        string $unit = self::UNIT_PER_TOKEN,
        ?string $rateInput = null,
        ?string $rateOutput = null,
        ?string $rateCacheRead = null,
        ?string $rateCacheWrite = null,
        ?string $rateAny = null,
        ?\DateTimeImmutable $createdAt = null,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rateInput = $rateInput;
        $this->rateOutput = $rateOutput;
        $this->rateCacheRead = $rateCacheRead;
        $this->rateCacheWrite = $rateCacheWrite;
        $this->rateAny = $rateAny;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}
