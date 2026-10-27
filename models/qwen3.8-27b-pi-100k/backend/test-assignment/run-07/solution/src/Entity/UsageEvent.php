<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
final class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /**
     * Код провайдера из потока событий (часть естественного ключа дедупликации).
     */
    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $provider;

    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $providerRequestId;

    /**
     * Код модели провайдера.
     */
    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $model;

    /**
     * Идентификатор пользователя («user» во входных данных);
     * колонка — user_login, т.к. «user» — зарезервированное слово PostgreSQL.
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $userLogin;

    /**
     * Момент запроса (UTC).
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensInput;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensOutput;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensCacheRead;

    #[ORM\Column(type: Types::BIGINT)]
    private int $tokensCacheWrite;

    /**
     * pending — ещё не рассчитано (в т.ч. unrated), rated — рассчитано.
     */
    #[ORM\Column(type: Types::STRING, length: 16)]
    private string $status = self::STATUS_PENDING;

    /**
     * Округлённая стоимость события — десятичная строка, ровно 6 знаков.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cost = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true)]
    private ?PriceRow $priceRow = null;

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
        $this->userLogin = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->status = self::STATUS_PENDING;
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
        return $this->userLogin;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    /**
     * Фиксирует расчёт события: статус, итоговая (округлённая) стоимость
     * и применённая строка цен (нужна для reprice).
     */
    public function markRated(Money $cost, PriceRow $priceRow): void
    {
        $this->status = self::STATUS_RATED;
        $this->cost = $cost->toString();
        $this->priceRow = $priceRow;
    }

    /**
     * Обновляет стоимость уже рассчитанного события (retro-коррекция цены).
     */
    public function resetCost(Money $cost): void
    {
        $this->cost = $cost->toString();
    }
}
