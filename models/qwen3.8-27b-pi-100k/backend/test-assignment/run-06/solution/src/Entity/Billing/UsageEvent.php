<?php

declare(strict_types=1);

namespace App\Entity\Billing;

use App\Domain\Billing\EventStatus;
use App\Domain\Billing\UsageEventDraft;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнал провайдера). Натуральный ключ —
 * (provider, provider_request_id): повторная доставка игнорируется.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 64)]
    private string $provider = '';

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId = '';

    #[ORM\Column(type: 'string', length: 64)]
    private string $model = '';

    #[ORM\Column(name: 'user_login', type: 'string', length: 255)]
    private string $user = '';

    /** Момент запроса (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    /**
     * @internal только для импорта/создания
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
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public static function fromDraft(UsageEventDraft $draft): self
    {
        return new self(
            $draft->provider,
            $draft->providerRequestId,
            $draft->model,
            $draft->user,
            $draft->ts,
            $draft->tokensInput,
            $draft->tokensOutput,
            $draft->tokensCacheRead,
            $draft->tokensCacheWrite,
        );
    }

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = EventStatus::Pending->value;

    /** Строка цен, по которой событие тарифицировано (null до rate). */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $priceRowId = null;

    /** Итоговая стоимость в USD, десятичная строка, 6 знаков (bcmath). */
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $costUsd = null;

    public function status(): EventStatus
    {
        return EventStatus::fromString($this->status);
    }

    public function setRated(int $priceRowId, string $costUsd): void
    {
        $this->status = EventStatus::Rated->value;
        $this->priceRowId = $priceRowId;
        $this->costUsd = $costUsd;
    }

    public function id(): int
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

    public function priceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function costUsd(): ?string
    {
        return $this->costUsd;
    }
}
