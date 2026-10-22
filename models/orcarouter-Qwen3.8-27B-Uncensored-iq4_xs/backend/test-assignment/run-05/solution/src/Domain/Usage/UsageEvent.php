<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\ValueObject\TokenUsage;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (запрос к модели).
 *
 * Естественный ключ — (provider, provider_request_id); повторная доставка
 * игнорируется. Состояние тарификации:
 *  - rated = false — ещё не рассчитано (будет повторный прогон в app:billing:rate);
 *  - rated = true, cost — округлённая до 6 знаков стоимость события;
 *    appliedPriceValidFrom — ключ строки цен, которой тарифицировано
 *    (нужно для ретро-коррекции цены).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_rated', columns: ['rated'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_events_applied_price', columns: ['provider', 'model', 'applied_price_valid_from'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $provider = '';

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 512)]
    private string $providerRequestId = '';

    #[ORM\Column(type: 'string', length: 128)]
    private string $model = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $rated = false;

    /** Ровно 6 знаков после точки; null, пока событие не рассчитано (numeric(28,6)). */
    #[ORM\Column(type: 'decimal', precision: 28, scale: 6, nullable: true)]
    private ?string $cost = null;

    #[ORM\Column(name: 'applied_price_valid_from', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $appliedPriceValidFrom = null;

    public static function naturalKey(string $provider, string $providerRequestId): string
    {
        return $provider.'/'.$providerRequestId;
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

    public function ts(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function tokens(): TokenUsage
    {
        return TokenUsage::fromKinds([
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ]);
    }

    public function rated(): bool
    {
        return $this->rated;
    }

    /** @return string|null стоимость события, 6 знаков, или null */
    public function cost(): ?string
    {
        return $this->cost;
    }

    public function appliedPriceValidFrom(): ?\DateTimeImmutable
    {
        return $this->appliedPriceValidFrom;
    }

    public function set(string $provider, string $providerRequestId, string $model, string $user, \DateTimeImmutable $ts, int $tokensInput, int $tokensOutput, int $tokensCacheRead, int $tokensCacheWrite): void
    {
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

    /** Зафиксировать результат расчёта (округлённая стоимость + key строки цен). */
    public function markRated(string $cost6, \DateTimeImmutable $priceValidFrom): void
    {
        $this->rated = true;
        $this->cost = $cost6;
        $this->appliedPriceValidFrom = $priceValidFrom;
    }

    /** Пересчёт после ретро-коррекции цены. */
    public function setCost(string $cost6): void
    {
        $this->cost = $cost6;
    }
}
