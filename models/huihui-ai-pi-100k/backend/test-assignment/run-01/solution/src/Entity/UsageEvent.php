<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Decimal;
use App\Domain\EventStatus;
use App\Domain\TokenUsage;
use App\Util\DateTimeUtc;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнал провайдера).
 *
 * Естественный ключ: (provider, provider_request_id) — повторная доставка
 * такого события целиком игнорируется, без обновлений и доначислений.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider_id', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_events_lookup', columns: ['provider_id', 'model_id', 'status'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    private Provider $provider;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    private Model $model;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $user;

    /** Момент запроса (UTC). */
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensInput;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensOutput;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensCacheRead;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensCacheWrite;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: EventStatus::class, options: ['default' => 'pending'])]
    private EventStatus $status = EventStatus::Pending;

    /** Строка цен, по которой событие рассчитано (для rated). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true)]
    private ?PriceRow $priceRow = null;

    /** Итоговая стоимость события, USD, с 6 знаками после точки. */
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 6, nullable: true)]
    private ?string $cost = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ratedAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        Provider $provider,
        string $providerRequestId,
        Model $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->createdAt = DateTimeUtc::now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
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

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    /** Строка стоимости в 6 знаков (или null, если событие unrated). */
    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function getRatedAt(): ?\DateTimeImmutable
    {
        return $this->ratedAt;
    }

    public function getTokenUsage(): TokenUsage
    {
        return new TokenUsage(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        );
    }

    /**
     * @param Decimal $cost уже округлённая стоимость (HALF_UP, 6 знаков)
     */
    public function markRated(PriceRow $priceRow, Decimal $cost): void
    {
        $this->status = EventStatus::Rated;
        $this->priceRow = $priceRow;
        $this->setCost($cost);
        $this->ratedAt = DateTimeUtc::now();
    }

    public function markUnrated(): void
    {
        $this->status = EventStatus::Unrated;
    }

    public function setCost(Decimal $cost): void
    {
        $this->cost = $cost->format(6);
    }
}
