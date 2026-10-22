<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Billing\TokenCounts;
use App\Domain\Pricing\Model;
use App\Domain\Pricing\Provider;
use App\Domain\Pricing\PriceRow;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования LLM, поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id); повторная доставка
 * уже известной пары целиком игнорируется (п. 5 задания).
 *
 * До расчёта — статус pending, cost null; после `app:billing:rate` —
 * rated с зафиксированной строкой цен и стоимостью (уже округлённой до 6).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\TypedIdentifier]
    #[ORM\Column(type: 'bigint')]
    private null|int $id = null;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    private string $providerRequestId;

    #[ORM\Column(name: 'user', type: 'string', length: 255)]
    private string $user;

    #[ORM\Column(name: 'ts', type: 'datetime_immutable')]
    private DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'string', length: 16, enumType: UsageStatus::class, options: ['default' => 'pending'])]
    private UsageStatus $status = UsageStatus::Pending;

    /** Стоимость события в USD, округлённая до 6 знаков (п. 4 задания). */
    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, options: ['default' => null])]
    private null|string $cost = null;

    #[ORM\Column(name: 'rated_at', type: 'datetime_immutable', options: ['default' => null])]
    private null|DateTimeImmutable $ratedAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    private Model $model;

    /** Строка цен, по которой событие тарифицировано (null, пока unrated). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    private null|PriceRow $priceRow = null;

    public function __construct(
        Provider $provider,
        Model $model,
        string $providerRequestId,
        string $user,
        DateTimeImmutable $ts,
        TokenCounts $tokens,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->providerRequestId = $providerRequestId;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokens->input;
        $this->tokensOutput = $tokens->output;
        $this->tokensCacheRead = $tokens->cacheRead;
        $this->tokensCacheWrite = $tokens->cacheWrite;
        $this->status = UsageStatus::Pending;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * Фиксация результата расчёта («одно событие — одна стоимость»).
     */
    public function markRated(string $cost, PriceRow $priceRow, DateTimeImmutable $ratedAt): void
    {
        $this->status = UsageStatus::Rated;
        $this->cost = $cost;
        $this->priceRow = $priceRow;
        $this->ratedAt = $ratedAt;
    }

    /**
     * Обновление стоимости после ретро-коррекции цены (статус rated сохраняется).
     */
    public function changeRatedCost(string $cost): void
    {
        $this->cost = $cost;
    }

    public function getId(): null|int
    {
        return $this->id;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): DateTimeImmutable
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

    public function getTokenCounts(): TokenCounts
    {
        return new TokenCounts(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        );
    }

    public function getStatus(): UsageStatus
    {
        return $this->status;
    }

    public function getCost(): null|string
    {
        return $this->cost;
    }

    public function getRatedAt(): null|DateTimeImmutable
    {
        return $this->ratedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getPriceRow(): null|PriceRow
    {
        return $this->priceRow;
    }
}
