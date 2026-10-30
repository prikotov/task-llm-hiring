<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

use App\Billing\Domain\Catalog\Price;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования модели. Естественный ключ: (provider, provider_request_id).
 * Пока price == null, событие unrated и в отчёт не попадает.
 */
#[ORM\Entity(repositoryClass: \App\Billing\Infrastructure\Persistence\UsageEventRepository::class)]
#[ORM\Table(name: 'billing_usage_event')]
#[ORM\UniqueConstraint(name: 'uniq_billing_usage_event_key', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_billing_usage_event_ts', columns: ['ts'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', length: 64)]
    private string $provider;

    #[ORM\Column(name: 'provider_request_id', length: 128)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', length: 128)]
    private string $model;

    #[ORM\Column(name: 'user_id', length: 255)]
    private string $user;

    #[ORM\Column(name: 'ts', type: 'datetimetz_immutable', precision: 6)]
    private DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write')]
    private int $tokensCacheWrite;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', nullable: true)]
    private ?Price $price = null;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    public function __construct(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        DateTimeImmutable $ts,
        TokenUsage $usage,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $usage->input;
        $this->tokensOutput = $usage->output;
        $this->tokensCacheRead = $usage->cacheRead;
        $this->tokensCacheWrite = $usage->cacheWrite;
    }

    /** Тарификация (или перетарификация) событием строкой цен. */
    public function rateWith(Price $price): void
    {
        $this->costUsd = $price->charge($this->usage(), $this->ts);
        $this->price = $price;
    }

    public function isRated(): bool
    {
        return $this->price !== null;
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

    public function model(): string
    {
        return $this->model;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function ts(): DateTimeImmutable
    {
        return $this->ts;
    }

    public function usage(): TokenUsage
    {
        return new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite);
    }

    public function price(): ?Price
    {
        return $this->price;
    }

    public function costUsd(): ?string
    {
        return $this->costUsd;
    }
}
