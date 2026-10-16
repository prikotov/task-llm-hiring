<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования токенов, доставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id): повторная доставка
 * такого же события игнорируется целиком (никаких обновлений/доначислений).
 *
 * Статусы:
 *  - pending: ещё не удалось рассчитать (нет модели/строки цены);
 *  - rated:   рассчитано, стоимость зафиксирована в costUsd.
 * Каждый запуск app:billing:rate повторно пробует все pending-события.
 */
#[ORM\Entity]
#[ORM\Table(
    name: 'usage_events',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id']),
    ],
)]
final class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $provider;

    #[ORM\Column(name: 'provider_request_id', type: Types::STRING, length: 255)]
    private string $providerRequestId;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $model;

    #[ORM\Column(name: 'user_ref', type: Types::STRING, length: 255)]
    private string $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: Types::BIGINT)]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: Types::BIGINT)]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: Types::BIGINT)]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: Types::BIGINT)]
    private int $tokensCacheWrite;

    #[ORM\Column(type: Types::STRING, length: 16)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'price_row_id', type: Types::INTEGER, nullable: true)]
    private ?int $priceRowId = null;

    /** Точная (неокруглённая) стоимость до округления события, decimal-строка. */
    #[ORM\Column(name: 'base_cost', type: Types::STRING, length: 64, nullable: true)]
    private ?string $baseCost = null;

    #[ORM\Column(name: 'off_peak_applied', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $offPeakApplied = false;

    /** Итоговая стоимость события: ровно одно округление HALF_UP до 6 знаков. */
    #[ORM\Column(name: 'cost_usd', type: Types::STRING, length: 64, nullable: true)]
    private ?string $costUsd = null;

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

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): string
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

    /** @return array{input:int,output:int,cache_read:int,cache_write:int} */
    public function getTokens(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    public function getPriceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function markRated(int $priceRowId, string $baseCost, bool $offPeakApplied, string $costUsd): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRowId = $priceRowId;
        $this->baseCost = $baseCost;
        $this->offPeakApplied = $offPeakApplied;
        $this->costUsd = $costUsd;
    }
}
