<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\BillingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (фактический запрос к модели), поставленное провайдером.
 *
 * Естественный ключ дедупликации — (provider, provider_request_id).
 *
 * Стоимость (`cost`) — итог тарификации, хранится после округления до 6 знаков.
 * Ссылка на использованную строку цены (`priceRow`) — для коррекции (reprice).
 * События с `rated = false` (unrated) в отчёт не попадают.
 */
#[ORM\Entity(repositoryClass: BillingRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_provider_request', columns: ['provider_id', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_event_provider', fields: ['provider'])]
#[ORM\Index(name: 'idx_usage_event_model', fields: ['model'])]
#[ORM\Index(name: 'idx_usage_event_price_row', fields: ['priceRow'])]
#[ORM\Index(name: 'idx_usage_event_ts', fields: ['ts'])]
#[ORM\Index(name: 'idx_usage_event_rated', fields: ['rated'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    /** Идентификатор запроса со стороны провайдера. */
    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: TokenModel::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private TokenModel $model;

    /** Идентификатор пользователя (строка; домена пользователей нет). */
    #[ORM\Column(name: 'user_login', type: 'string', length: 255)]
    private string $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $rated = false;

    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true)]
    private ?PriceRow $priceRow = null;

    /** Итоговая стоимость события, USD, 6 знаков после округления. */
    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $cost = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function setProviderRequestId(string $providerRequestId): self
    {
        $this->providerRequestId = $providerRequestId;

        return $this;
    }

    public function getModel(): TokenModel
    {
        return $this->model;
    }

    public function setModel(TokenModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function setTs(\DateTimeImmutable $ts): self
    {
        $this->ts = $ts;

        return $this;
    }

    public function getTokensInput(): int
    {
        return $this->tokensInput;
    }

    public function setTokensInput(int $tokensInput): self
    {
        $this->tokensInput = $tokensInput;

        return $this;
    }

    public function getTokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function setTokensOutput(int $tokensOutput): self
    {
        $this->tokensOutput = $tokensOutput;

        return $this;
    }

    public function getTokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function setTokensCacheRead(int $tokensCacheRead): self
    {
        $this->tokensCacheRead = $tokensCacheRead;

        return $this;
    }

    public function getTokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function setTokensCacheWrite(int $tokensCacheWrite): self
    {
        $this->tokensCacheWrite = $tokensCacheWrite;

        return $this;
    }

    public function isRated(): bool
    {
        return $this->rated;
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function setPriceRow(?PriceRow $priceRow): self
    {
        $this->priceRow = $priceRow;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Помечает событие рассчитанным: фиксирует цену, строку цен, дату изменения.
     */
    public function markRated(PriceRow $priceRow, string $cost): self
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->rated = true;
        $this->priceRow = $priceRow;
        $this->cost = $cost;
        $this->updatedAt = $now;

        return $this;
    }
}
