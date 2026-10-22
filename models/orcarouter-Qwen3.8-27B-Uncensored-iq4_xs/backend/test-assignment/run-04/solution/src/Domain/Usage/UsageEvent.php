<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Money\Money;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнал провайдера).
 *
 * Естественный ключ — (provider, provider_request_id): повторная доставка
 * такого события целиком игнорируется (идемпотентность приёма).
 *
 * Все времена — UTC.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\UniqueConstraint(name: 'uq_usage_events_natural_key', columns: ['provider', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $provider;

    #[ORM\Column(type: 'string')]
    private string $providerRequestId;

    #[ORM\Column(name: 'user_name', type: 'string')]
    private string $user;

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

    #[ORM\Column(type: 'string', length: 16, enumType: BillingStatus::class)]
    private BillingStatus $status;

    /** Строка цены, по которой событие рассчитано (после rate / reprice). */
    #[ORM\ManyToOne(PriceRow::class, fetch: 'EAGER')]
    private ?PriceRow $priceRow = null;

    /**
     * Итоговая стоимость события — ровно 6 знаков, HALF_UP (десятичная строка,
     * float для денег запрещён). null, пока событие не рассчитано.
     */
    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 30, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\ManyToOne(Model::class, fetch: 'EAGER')]
    private Model $model;

    public function __construct(
        string $provider,
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
        $this->status = BillingStatus::Unrated;
    }

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

    public function model(): Model
    {
        return $this->model;
    }

    public function user(): string
    {
        return $this->user;
    }

    /**
     * Момент запроса (UTC).
     */
    public function ts(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function tokenCount(TokenKind $kind): int
    {
        return match ($kind) {
            TokenKind::Input => $this->tokensInput,
            TokenKind::Output => $this->tokensOutput,
            TokenKind::CacheRead => $this->tokensCacheRead,
            TokenKind::CacheWrite => $this->tokensCacheWrite,
        };
    }

    public function totalTokens(): int
    {
        return $this->tokensInput + $this->tokensOutput + $this->tokensCacheRead + $this->tokensCacheWrite;
    }

    public function status(): BillingStatus
    {
        return $this->status;
    }

    public function isRated(): bool
    {
        return $this->status === BillingStatus::Rated;
    }

    public function priceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    /**
     * Стоимость события (есть только у рассчитанных).
     */
    public function money(): Money
    {
        if (! $this->isRated() || $this->costUsd === null) {
            throw new \LogicException(sprintf(
                'Event %s/%s is not rated; no cost stored.',
                $this->provider,
                $this->providerRequestId,
            ));
        }

        return Money::fromFixed6($this->costUsd);
    }

    /**
     * Фиксирует расчёт: цена, по которой тарифицировано, и итоговая стоимость (6 знаков).
     */
    public function rateWith(PriceRow $priceRow, Money $cost): void
    {
        $this->priceRow = $priceRow;
        $this->costUsd = $cost->asFixed6String();
        $this->status = BillingStatus::Rated;
    }

    /**
     * Ретро-коррекция (reprice): перезаписывает цену и стоимость;
     * событие уже было rated и остаётся rated.
     */
    public function repriceWith(PriceRow $priceRow, Money $cost): void
    {
        if (! $this->isRated()) {
            throw new \LogicException('repriceWith() applies to rated events only.');
        }

        $this->priceRow = $priceRow;
        $this->costUsd = $cost->asFixed6String();
    }
}
