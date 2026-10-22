<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (запрос к модели), поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id); повторная доставка
 * дубля игнорируется. Событие существует в двух состояниях:
 * pending (ещё не рассчитано, включая unrated) и rated.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_event', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    public const TOKEN_FIELDS = [
        'tokens_input',
        'tokens_output',
        'tokens_cache_read',
        'tokens_cache_write',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: 'string', length: 64)]
    private string $modelCode;

    /** Идентификатор пользователя; домена пользователей нет. */
    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    private string $user;

    /** Момент запроса (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'string', length: 8, options: ['default' => 'pending'])]
    private string $status = self::STATUS_PENDING;

    /** Ссылка на строку цен, по которой событие рассчитано. */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id')]
    private ?PriceRow $priceRow = null;

    /**
     * Окончательная стоимость события, USD: ровно 6 знаков после точки,
     * десятичная строка (float для денег не используется).
     */
    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $user,
        DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): DateTimeImmutable
    {
        return $this->ts;
    }

    /**
     * @return array{input: int, output: int, cache_read: int, cache_write: int}
     */
    public function tokens(): array
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

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function markRated(PriceRow $priceRow, string $costUsd): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRow = $priceRow;
        $this->costUsd = $costUsd;
    }

    /** Смена стоимости уже рассчитанного события (ретро-коррекция цены). */
    public function setCostUsd(string $costUsd): void
    {
        $this->costUsd = $costUsd;
    }
}
