<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Billing\TokenUsage;
use App\Repository\UsageEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (фактический запрос к модели), поставленное провайдером.
 *
 * - естественный ключ — (provider_code, provider_request_id);
 * - provider_code/model_code — коды из справочника, хранят «как прислал»
 *   (события могут приходить раньше, чем справка о провайдере/модели
 *   — такие события до получения цен остаются unrated);
 * - cost == null — событие ещё не рассчитано (unrated);
 * - рассчитанное событие хранит итоговую стоимость (6 знаков) и строку цен,
 *   по которой она рассчитана — это опорный факт для reprice.
 */
#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_events_price_id', columns: ['price_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 128)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: Types::STRING, length: 128)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: Types::STRING, length: 128)]
    private string $modelCode;

    // Колонка названа username, а не user: USER — зарезервированное слово PostgreSQL.
    #[ORM\Column(name: 'username', type: Types::STRING, length: 128)]
    private string $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensInput;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensOutput;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheRead;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheWrite;

    /**
     * Стоимость события в USD, ровно 6 знаков после точки;
     * null — событие ещё не тарифицировано (unrated).
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 6, nullable: true)]
    private ?string $cost = null;

    /** Идентификатор строки цен (prices), по которой рассчитано событие. */
    #[ORM\Column(name: 'price_id', type: Types::INTEGER, nullable: true)]
    private ?int $priceId = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $user,
        \DateTimeImmutable $ts,
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

    public function id(): ?int
    {
        return $this->id;
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

    public function cost(): ?string
    {
        return $this->cost;
    }

    public function priceId(): ?int
    {
        return $this->priceId;
    }

    /** Событие рассчитано, если стоимость присвоена (в т.ч. 0.000000). */
    public function isRated(): bool
    {
        return $this->cost !== null;
    }

    public function tokenUsage(): TokenUsage
    {
        return new TokenUsage(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        );
    }

    /** Модель в формате отчёта: <provider>/<code>. */
    public function displayModel(): string
    {
        return $this->providerCode.'/'.$this->modelCode;
    }

    /** Присвоить рассчитанную стоимость и строку цен, по которой она получена. */
    public function applyRating(string $cost, int $priceId): void
    {
        $this->cost = $cost;
        $this->priceId = $priceId;
    }

    /** Снять расчёт: событие снова unrated. */
    public function clearRating(): void
    {
        $this->cost = null;
        $this->priceId = null;
    }
}
