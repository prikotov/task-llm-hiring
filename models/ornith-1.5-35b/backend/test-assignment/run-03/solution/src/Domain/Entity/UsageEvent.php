<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\UsageEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(
    name: 'uniq_usage_event',
    columns: ['provider_id', 'provider_request_id'],
)]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * код провайдера. Вместе с provider_request_id — ключ события.
     * Отдельный столбец (не только FK к Provider) для идемпотентности:
     * дедупликация по строковому ключу независимо от наличия справочника.
     */
    #[ORM\Column(type: 'string')]
    public string $providerId = '';

    #[ORM\Column(name: 'provider_request_id', type: 'string')]
    public string $providerRequestId = '';

    #[ORM\Column(name: 'user', type: 'string')]
    public string $user = '';

    /** <provider>/<code> для отчёта. */
    #[ORM\Column(type: 'string')]
    public string $model = '';

    #[ORM\Column(name: 'ts', type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    public int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    public int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'boolean')]
    public bool $rated = false;

    /** округлённая стоимость события (6 знаков); null, если не тарифицировано. */
    #[ORM\Column(name: 'cost_usd', type: 'decimal', nullable: true, precision: 40, scale: 6)]
    public ?string $costUsd = null;

    /** FK на PriceRow, по которой был тарифицирован event (для reprice). */
    #[ORM\Column(name: 'price_row_id', type: 'integer', nullable: true)]
    public ?int $priceRowId = null;

    public function __construct(\DateTimeImmutable $ts)
    {
        $this->ts = $ts;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
