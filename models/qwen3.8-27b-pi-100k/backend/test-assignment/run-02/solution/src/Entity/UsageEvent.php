<?php

declare(strict_types=1);

namespace App\Entity;

use App\Shared\Math\Decimal;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (фактический запрос к модели), поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id): повторная доставка
 * уже известного события целиком игнорируется (дедупликация при приёме).
 *
 * Жизненный цикл статуса:
 *   new     — принято, ещё не тарифицировано;
 *   rated   — стоимость рассчитана (costUsd, priceRow) — финальное состояние
 *             для обычных импортов цен; меняется только через reprice;
 *   unrated — тарификация не удалась (нет строки цены с valid_from ≤ ts),
 *             каждый следующий запуск rate повторяет попытку.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider_id', 'provider_request_id'])]
final class UsageEvent
{
    public const STATUS_NEW = 'new';
    public const STATUS_RATED = 'rated';
    public const STATUS_UNRATED = 'unrated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    /** Идентификатор запроса со стороны провайдера. */
    #[ORM\Column(length: 128)]
    private string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    /** Идентификатор пользователя (домена пользователей нет). Колонка «user_name», т.к. «user» — зарезервированное слово PostgreSQL. */
    #[ORM\Column(name: 'user_name', length: 255)]
    private string $user;

    /** Момент запроса, UTC. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    /** Исходная запись ts из фид-файла (например, с дробными секундами) — для точного отображения. */
    #[ORM\Column(length: 64)]
    private string $tsRaw;

    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    private int $tokensInput;

    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    private int $tokensOutput;

    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    private int $tokensCacheRead;

    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    private int $tokensCacheWrite;

    #[ORM\Column(length: 16)]
    private string $status;

    /** Строка цены, по которой тарифицировано событие (rated-события). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true)]
    private ?PriceRow $priceRow = null;

    /**
     * Итоговая стоимость в USD, 6 знаков после точки.
     * Заполняется только для rated-событий; в отчётах суммируется без
     * повторного округления (слагаемые уже округлены при тарификации).
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 38, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    /** Момент первой успешной тарификации, UTC. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ratedAt = null;

    /**
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     */
    public function __construct(
        Provider $provider,
        string $providerRequestId,
        Model $model,
        string $user,
        \DateTimeImmutable $ts,
        string $tsRaw,
        array $tokens,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tsRaw = $tsRaw;
        $this->tokensInput = $tokens['input'];
        $this->tokensOutput = $tokens['output'];
        $this->tokensCacheRead = $tokens['cache_read'];
        $this->tokensCacheWrite = $tokens['cache_write'];
        $this->status = self::STATUS_NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): Model
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

    public function getTsRaw(): string
    {
        return $this->tsRaw;
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

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function getRatedAt(): ?\DateTimeImmutable
    {
        return $this->ratedAt;
    }

    /** Успешная тарификация: стоимость зафиксирована (единственное округление уже сделано). */
    public function markRated(PriceRow $priceRow, string $costUsd, \DateTimeImmutable $ratingMoment): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRow = $priceRow;
        $this->costUsd = Decimal::money6($costUsd);
        $this->ratedAt ??= $ratingMoment;
    }

    /** Не тарифицировано: строки цены нет — ожидаем появления (попытка повторится). */
    public function markUnrated(): void
    {
        $this->status = self::STATUS_UNRATED;
        $this->priceRow = null;
        $this->costUsd = null;
    }

    /**
     * Ретро-коррекция (app:billing:reprice): событие перетыкается другой
     * строкой цены / другими условиями, ratedAt не изменяется.
     */
    public function reprice(PriceRow $priceRow, string $costUsd): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRow = $priceRow;
        $this->costUsd = Decimal::money6($costUsd);
    }
}
