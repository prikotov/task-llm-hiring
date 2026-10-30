<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (фактический запрос к модели), поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id); повторная доставка
 * уже известного события игнорируется (идемпотентность приёма).
 */
#[ORM\Entity]
#[ORM\Table(name: 'billing_usage_event')]
#[ORM\UniqueConstraint(
    name: 'uniq_billing_usage_event_provider_request',
    columns: ['provider', 'provider_request_id'],
)]
class UsageEvent
{
    public function __construct(
        #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
        private ?int $id,

        // Код провайдера (составная часть естественного ключа).
        #[ORM\Column(type: 'string', length: 64)]
        private string $provider,

        // Идентификатор запроса со стороны провайдера.
        #[ORM\Column(type: 'string', length: 128)]
        private string $providerRequestId,

        // Код модели в рамках провайдера.
        #[ORM\Column(type: 'string', length: 128)]
        private string $model,

        #[ORM\Column(type: 'string', length: 255)]
        private string $user,

        // Момент запроса (всегда UTC).
        #[ORM\Column(type: 'timestamptz')]
        private \DateTimeImmutable $ts,

        #[ORM\Column(type: 'bigint')]
        private int $tokensInput = 0,

        #[ORM\Column(type: 'bigint')]
        private int $tokensOutput = 0,

        #[ORM\Column(type: 'bigint')]
        private int $tokensCacheRead = 0,

        #[ORM\Column(type: 'bigint')]
        private int $tokensCacheWrite = 0,

        #[ORM\Column(type: 'string', enumType: EventStatus::class)]
        private EventStatus $status = EventStatus::Unrated,

        // Итоговая стоимость события в USD, 6 знаков; null, пока не рассчитано.
        #[ORM\Column(type: 'string', length: 32, nullable: true)]
        private ?string $costUsd = null,

        // Строка цен, по которой событие рассчитано (для ретро-коррекции).
        #[ORM\ManyToOne(targetEntity: PriceRow::class)]
        #[ORM\JoinColumn(name: 'price_row_id', nullable: true)]
        private ?PriceRow $priceRow = null,
    ) {
    }

    public function getId(): ?int
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

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    /**
     * Фиксирует расчёт события стоимостью (уже округлённой до 6 знаков)
     * по конкретной строке цен.
     */
    public function markRated(string $costUsd, PriceRow $priceRow): void
    {
        $this->status = EventStatus::Rated;
        $this->costUsd = $costUsd;
        $this->priceRow = $priceRow;
    }
}
