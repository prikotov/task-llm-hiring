<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Billing\EventStatus;
use App\Domain\Billing\TokenCounts;
use App\Repository\UsageEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (запрос к модели).
 *
 * Естественный ключ — (provider, provider_request_id). Импортируется
 * идемпотентно: повторная доставка уже известного события игнорируется.
 *
 * Цена и её строка фиксируются в rate-момент: cost хранит единственный
 * округлённый итог события (6 знаков, HALF_UP), тип float не используется.
 */
#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_event_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'ix_event_ts', columns: ['ts'])]
#[ORM\Index(name: 'ix_event_status', columns: ['status'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $providerCode;

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $modelCode;

    #[ORM\Column(type: 'string', length: 128, name: 'user_name')]
    private string $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'string', length: 16, enumType: EventStatus::class)]
    private EventStatus $status;

    /**
     * Итоговая стоимость события (6 знаков, уже округлена), decimal-строка.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 6, nullable: true)]
    private ?string $cost = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ratedAt = null;

    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
    private ?PriceRow $priceRow = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $user,
        \DateTimeImmutable $ts,
        TokenCounts $counts,
        \DateTimeImmutable $createdAt,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $counts->input;
        $this->tokensOutput = $counts->output;
        $this->tokensCacheRead = $counts->cacheRead;
        $this->tokensCacheWrite = $counts->cacheWrite;
        $this->status = EventStatus::Pending;
        $this->createdAt = $createdAt;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function key(): array
    {
        return ['provider' => $this->providerCode, 'provider_request_id' => $this->providerRequestId];
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function providerRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function modelCode(): string
    {
        return $this->modelCode;
    }

    /**
     * Отображаемое имя модели в отчёте: provider/code.
     */
    public function modelName(): string
    {
        return $this->providerCode . '/' . $this->modelCode;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function ts(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function counts(): TokenCounts
    {
        return TokenCounts::fromArray([
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ]);
    }

    public function status(): EventStatus
    {
        return $this->status;
    }

    public function cost(): ?string
    {
        return $this->cost;
    }

    public function setCost(string $cost): void
    {
        $this->cost = $cost;
    }

    public function priceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    /**
     * Минуты внутри суток в UTC (для правила оф-пика).
     */
    public function minuteOfDayUtc(): int
    {
        $utc = $this->ts->setTimezone(new \DateTimeZone('UTC'));

        return $utc->format('G') * 60 + $utc->format('i');
    }

    public function markRated(PriceRow $priceRow, string $cost, \DateTimeImmutable $now): void
    {
        $this->status = EventStatus::Rated;
        $this->cost = $cost;
        $this->priceRow = $priceRow;
        $this->ratedAt = $now;
    }
}
