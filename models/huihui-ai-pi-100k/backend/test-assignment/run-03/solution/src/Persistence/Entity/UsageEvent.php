<?php

declare(strict_types=1);

namespace App\Persistence\Entity;

use App\Domain\Common\Iso8601Utc as Utc;
use App\Domain\Pricing\TokenUsage;
use App\Persistence\Enum\EventStatus;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (запрос к модели), поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id): повторная доставка
 * игнорируется. Коды провайдера/модели хранятся строками — событие может
 * предшествовать появлению справочника (такое событие остаётся unrated
 * до прихода цен).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status_id', columns: ['status', 'id'])]
#[ORM\Index(name: 'idx_usage_events_report', columns: ['status', 'ts'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    #[ORM\Column(type: 'text')]
    public string $provider = '';

    #[ORM\Column(type: 'text')]
    public string $model = '';

    #[ORM\Column(name: 'provider_request_id', type: 'text')]
    public string $providerRequestId = '';

    #[ORM\Column(name: '"user"', type: 'text')]
    public string $user = '';

    #[ORM\Column(type: 'datetimetz')]
    public DateTimeInterface $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    public int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    public int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 16, enumType: EventStatus::class)]
    public EventStatus $status = EventStatus::Pending;

    /** Идентификатор строки цен, по которой рассчитано (только для rated). */
    #[ORM\Column(name: 'price_row_id', type: 'bigint', nullable: true)]
    public ?int $priceRowId = null;

    /**
     * Итог единственного округления: стоимость события, 6 знаков после точки.
     * Суммы отчёта складывают именно эти значения, повторно не округляя.
     */
    #[ORM\Column(name: 'billed_cost', type: 'text', nullable: true)]
    public ?string $billedCost = null;

    public function tokenUsage(): TokenUsage
    {
        return new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite);
    }

    /** Момент события в UTC (храним как мгновенное время UTC). */
    public function tsUtc(): DateTimeImmutable
    {
        return Utc::reanchor($this->ts);
    }
}
