<?php

declare(strict_types=1);

namespace App\Usage\Domain;

use App\Pricing\Domain\PriceRow;
use App\Pricing\Domain\TokenUsage;
use App\Shared\Decimal;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования от провайдера. Естественный ключ — (provider, provider_request_id).
 * Модель хранится кодами: событие может прийти раньше справочника (тогда оно unrated).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_event')]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_natural_key', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_event_unrated', columns: ['rated_at'])]
#[ORM\Index(name: 'idx_usage_event_ts', columns: ['ts'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::BIGINT)]
    private int|string|null $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 255)]
    private string $providerRequestId;

    #[ORM\Column(length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_id', length: 255)]
    private string $user;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $ts;

    #[ORM\Column(type: Types::BIGINT)]
    private int|string $tokensInput;

    #[ORM\Column(type: Types::BIGINT)]
    private int|string $tokensOutput;

    #[ORM\Column(type: Types::BIGINT)]
    private int|string $tokensCacheRead;

    #[ORM\Column(type: Types::BIGINT)]
    private int|string $tokensCacheWrite;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?PriceRow $priceRow = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $ratedAt = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $user,
        DateTimeImmutable $ts,
        TokenUsage $tokens,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokens->input;
        $this->tokensOutput = $tokens->output;
        $this->tokensCacheRead = $tokens->cacheRead;
        $this->tokensCacheWrite = $tokens->cacheWrite;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function priceRow(): ?PriceRow
    {
        return $this->priceRow;
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

    public function ts(): DateTimeImmutable
    {
        return $this->ts;
    }

    public function tokens(): TokenUsage
    {
        return new TokenUsage((int) $this->tokensInput, (int) $this->tokensOutput, (int) $this->tokensCacheRead, (int) $this->tokensCacheWrite);
    }

    public function cost(): ?Decimal
    {
        return $this->costUsd === null ? null : Decimal::of($this->costUsd);
    }

    public function isRated(): bool
    {
        return $this->ratedAt !== null;
    }

    /** Тарифицирует событие указанной строкой цены. Возвращает новую стоимость. */
    public function rateWith(PriceRow $row, DateTimeImmutable $now): Decimal
    {
        $cost = $row->costOf($this->tokens(), $this->ts);
        $this->priceRow = $row;
        $this->costUsd = $cost->toFixed(6);
        $this->ratedAt = $now;

        return $cost;
    }
}
