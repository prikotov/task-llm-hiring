<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Usage\TokenBreakdown;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * События использования токенов, поставляемые провайдерами.
 * Natural key: (provider, providerRequestId) — дедупликация при импорте.
 *
 * Итоговая стоимость события хранится как строка с ровно 6 знаками —
 * единственное допустимое округление в pipeline (HALF_UP при расчёте).
 *
 * status: 'unrated' | 'rated'; ratedAt — время расчёта (audit, см. допущения PR).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'ux_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private int|null $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $provider = '';

    #[ORM\Column(type: Types::STRING, length: 191)]
    private string $providerRequestId = '';

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $modelCode = '';

    #[ORM\Column(type: Types::STRING, length: 191, name: 'user_code')]
    private string $user = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensInput = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensOutput = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(type: Types::STRING, length: 8)]
    private string $status = self::STATUS_UNRATED;

    /** Итоговая стоимость события в USD, строка с ровно 6 знаками — только для rated. */
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    private string|null $costUsd = null;

    /** id строки цен, которой тарифицировано событие (для reprice). */
    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private int|null $priceRowId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private \DateTimeImmutable|null $ratedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function id(): int
    {
        return $this->id;
    }

    public static function create(
        string $provider,
        string $providerRequestId,
        string $modelCode,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): self {
        $event = new self();
        $event->provider = $provider;
        $event->providerRequestId = $providerRequestId;
        $event->modelCode = $modelCode;
        $event->user = $user;
        $event->ts = $ts;
        $event->tokensInput = $tokensInput;
        $event->tokensOutput = $tokensOutput;
        $event->tokensCacheRead = $tokensCacheRead;
        $event->tokensCacheWrite = $tokensCacheWrite;
        $event->status = self::STATUS_UNRATED;
        $event->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        return $event;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    public function markRated(string $costUsd, int $priceRowId): void
    {
        $this->status = self::STATUS_RATED;
        $this->costUsd = $costUsd;
        $this->priceRowId = $priceRowId;
        $this->ratedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function tokens(): TokenBreakdown
    {
        return new TokenBreakdown(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        );
    }

    public function provider(): string { return $this->provider; }
    public function providerRequestId(): string { return $this->providerRequestId; }
    public function modelCode(): string { return $this->modelCode; }
    public function userCode(): string { return $this->user; }
    public function ts(): \DateTimeImmutable { return $this->ts; }
    public function tokensInput(): int { return $this->tokensInput; }
    public function tokensOutput(): int { return $this->tokensOutput; }
    public function tokensCacheRead(): int { return $this->tokensCacheRead; }
    public function tokensCacheWrite(): int { return $this->tokensCacheWrite; }
    public function status(): string { return $this->status; }
    public function costUsd(): ?string { return $this->costUsd; }
    public function priceRowId(): ?int { return $this->priceRowId; }
    public function ratedAt(): ?\DateTimeImmutable { return $this->ratedAt; }
}
