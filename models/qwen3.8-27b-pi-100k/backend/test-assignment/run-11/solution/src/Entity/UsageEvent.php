<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнал провайдеров).
 *
 * Естественный ключ (provider, provider_request_id) — дедупликация:
 * повторная доставка уже известного события целиком игнорируется
 * (без обновлений/доначислений), TASK.md п. 5.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    /** Тарифицировано; стоимость в cost_usd (пересчитывается только reprice). */
    public const STATUS_RATED = 'rated';
    /** В последнем rate-запуске цена не найдена — повторим на следующем. */
    public const STATUS_RETRYABLE = 'retryable';

    public const COST_SOURCE_RATE = 'rate';
    public const COST_SOURCE_REPRICE = 'reprice';

    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 64, nullable: false)]
    private string $provider = '';

    #[ORM\Column(name: 'provider_request_id', length: 128, nullable: false)]
    private string $providerRequestId = '';

    /** Код модели провайдера (без префикса). */
    #[ORM\Column(length: 128, nullable: false)]
    private string $model = '';

    #[ORM\Column(name: 'user_id', length: 255, nullable: false)]
    private string $user = '';

    /** Момент запроса, UTC (встроенный). */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?\DateTimeImmutable $ts = null;

    #[ORM\Column(name: 'tokens_input', type: Types::BIGINT, nullable: false)]
    private int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: Types::BIGINT, nullable: false)]
    private int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: Types::BIGINT, nullable: false)]
    private int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: Types::BIGINT, nullable: false)]
    private int $tokensCacheWrite = 0;

    /** pending | rated | retryable */
    #[ORM\Column(length: 16, nullable: false)]
    private string $status = self::STATUS_PENDING;

    /** Итоговая стоимость, десятичная строка ровно 6 знаков (HALF_UP при rate). */
    #[ORM\Column(name: 'cost_usd', type: Types::TEXT, nullable: true)]
    private ?string $costUsd = null;

    /** Кто последний раз менял стоимость: 'rate' | 'reprice' | null. */
    #[ORM\Column(name: 'cost_source', length: 16, nullable: true)]
    private ?string $costSource = null;

    /** ID строки прайса, по которой посчитана стоимость (для reprice). */
    #[ORM\Column(name: 'price_id', type: Types::BIGINT, nullable: true)]
    private ?int $priceId = null;

    /** Дата создания записи — для диагностики; не участвует в расчётах. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?\DateTimeImmutable $createdAt = null;

    /** Дата последнего изменения данных (для аудита / диагностики). */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?\DateTimeImmutable $updatedAt = null;

    /** @var Collection<UsageRating>|UsageRating[] (append-only журнал) */
    #[ORM\OneToMany(targetEntity: UsageRating::class, mappedBy: 'event', cascade: ['persist'])]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $ratings;

    /**
     * @param int $tokensInput
     * @param int $tokensOutput
     * @param int $tokensCacheRead
     * @param int $tokensCacheWrite
     */
    public function __construct(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->status = self::STATUS_PENDING;
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->ratings = new ArrayCollection();
    }

    // ---------- читаемый API ----------

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function providerRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function model(): string
    {
        return $this->model;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function ts(): \DateTimeImmutable
    {
        return $this->ts;
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

    public function status(): string
    {
        return $this->status;
    }

    /**
     * Итоговая текущая стоимость события, десятичная строка 6 знаков
     * (null — ещё не тарифицировано).
     */
    public function costUsd(): ?string
    {
        return $this->costUsd;
    }

    public function pricedAt(): ?\DateTimeImmutable
    {
        return $this->pricedAt;
    }

    public function priceId(): ?int
    {
        return $this->priceId;
    }

    public function costSource(): ?string
    {
        return $this->costSource;
    }

    /** @return Collection<UsageRating>|UsageRating[] */
    public function ratings(): Collection
    {
        return $this->ratings;
    }

    // ---------- мутаторы (для сервисов) ----------

    public function markPending(): void
    {
        $this->status = self::STATUS_PENDING;
        $this->costUsd = null;
        $this->costSource = null;
        $this->priceId = null;
        $this->touch();
    }

    public function markRated(string $costUsd, int $priceId): void
    {
        $this->status = self::STATUS_RATED;
        $this->costUsd = $costUsd;
        $this->costSource = self::COST_SOURCE_RATE;
        $this->priceId = $priceId;
        $this->touch();
    }

    public function applyReprice(string $costUsd, int $priceId): void
    {
        $this->status = self::STATUS_RATED;
        $this->costUsd = $costUsd;
        $this->costSource = self::COST_SOURCE_REPRICE;
        $this->priceId = $priceId;
        $this->touch();
    }

    public function tryMarkRetryable(): void
    {
        if ($this->costUsd === null) {
            $this->status = self::STATUS_RETRYABLE;
            $this->touch();
        }
    }

    public function attachRating(UsageRating $rating): void
    {
        if ($this->ratings->contains($rating)) {
            return;
        }
        $this->ratings->add($rating);
        $rating->assign($this);
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}
