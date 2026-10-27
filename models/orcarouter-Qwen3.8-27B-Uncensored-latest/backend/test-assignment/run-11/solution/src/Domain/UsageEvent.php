<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнальная запись). Натуральный ключ —
 * (provider_code, provider_request_id); повторная доставка игнорируется.
 *
 * Состояния:
 *  - pending — импортировано, ещё не тарифицировано;
 *  - rated   — рассчитано, стоимость зафиксирована (cost, price_row_id);
 *  - unrated — попытки были, но строки цены не нашлось (будут перепробованы).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_event_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(columns: ['status'], name: 'ix_event_status')]
#[ORM\Index(columns: ['ts'], name: 'ix_event_ts')]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';
    public const STATUS_UNRATED = 'unrated';

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

    #[ORM\Column(type: 'string', length: 128)]
    private string $userName;

    /** Момент запроса (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite;

    /** pending|rated|unrated */
    #[ORM\Column(type: 'string', length: 16)]
    private string $status;

    /**
     * Стоимость (Money, строка) после окончательного округления.
     * Null, пока событие не рассчитано (pending/unrated).
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $cost = null;

    /** Строка цены, применившаяся к событию (null, пока не рассчитано). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?PriceRow $priceRow = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $userName,
        \DateTimeImmutable $ts,
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
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->status = self::STATUS_PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function getTokenStats(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
    }

    public function getTokensInput(): int
    {
        return $this->tokensInput;
    }

    public function getTokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function getTokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function getTokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    public function isUnrated(): bool
    {
        return $this->status === self::STATUS_UNRATED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** Стоимость (Money), если рассчитано; null иначе. */
    public function getCost(): ?Money
    {
        if ($this->cost === null) {
            return null;
        }

        return Money::fromString($this->cost);
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    /**
     * Фиксирует результат первого расчёта (или пересчёта).
     */
    public function applyRating(PriceRow $row, Money $cost): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRow = $row;
        $this->cost = $cost->getAmount();
    }

    public function markUnrated(): void
    {
        $this->status = self::STATUS_UNRATED;
    }

    /** Возвращает событие в неизменённое состояние для повторного расчёта. */
    public function resetToPending(): void
    {
        $this->status = self::STATUS_PENDING;
        $this->cost = null;
        $this->priceRow = null;
    }
}
