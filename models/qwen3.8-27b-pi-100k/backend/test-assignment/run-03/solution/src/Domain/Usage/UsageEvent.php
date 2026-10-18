<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Catalog\PriceRow;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования: один запрос к модели по данным провайдера.
 *
 * Журнал событий неизменяем при приёме: дедупликация по естественному
 * ключу (providerCode, providerRequestId); повторная доставка
 * уже известного события целиком игнорируется.
 *
 * Провайдер и модель хранятся строками кодов (без FK): справочник цен
 * может догонять журнал позже, unrated-события — штатное состояние.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerCode;

    /** Идентификатор запроса со стороны провайдера. */
    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId;

    #[ORM\Column(type: 'string', length: 128)]
    private string $modelCode;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userName;

    /** Момент запроса (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    /** Исходная строка метки времени (для точного эха в служебных сообщениях). */
    #[ORM\Column(type: 'string', length: 64)]
    private string $tsRaw;

    #[ORM\Column(type: 'integer')]
    private int $tokensInput;

    #[ORM\Column(type: 'integer')]
    private int $tokensOutput;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'string', enumType: EventStatus::class, length: 16)]
    private EventStatus $status = EventStatus::Pending;

    /** Итоговая стоимость события, USD, ровно 6 знаков; null, пока не рассчитано. */
    #[ORM\Column(type: 'decimal', precision: 38, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    /** Строка цены, которой тарифицировано событие (основа ретро-коррекции). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?PriceRow $priceRow = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $userName,
        \DateTimeImmutable $ts,
        string $tsRaw,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->userName = $userName;
        $this->ts = $ts;
        $this->tsRaw = $tsRaw;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->status = EventStatus::Pending;
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function id(): int
    {
        return $this->id;
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

    public function userName(): string
    {
        return $this->userName;
    }

    public function ts(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function tsRaw(): string
    {
        return $this->tsRaw;
    }

    public function tokensInput(): int
    {
        return $this->tokensInput;
    }

    public function tokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function tokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function tokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function status(): EventStatus
    {
        return $this->status;
    }

    /** @return string|null стоимость, USD */
    public function costUsd(): ?string
    {
        return $this->costUsd;
    }

    public function priceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function markRated(PriceRow $priceRow, string $costUsd): void
    {
        $this->priceRow = $priceRow;
        $this->costUsd = $costUsd;
        $this->status = EventStatus::Rated;
    }

    public function markUnrated(): void
    {
        $this->status = EventStatus::Unrated;
    }

    /** Прямая перенасчитка стоимости при ретро-коррекции цены. */
    public function replaceCost(string $costUsd): void
    {
        $this->costUsd = $costUsd;
    }
}
