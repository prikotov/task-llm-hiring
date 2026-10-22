<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Catalog\PriceLine;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования — факт запроса к модели, поставленный провайдером.
 *
 * Естественный ключ: (provider, providerRequestId); повторная доставка
 * таких событий игнорируется целиком (идемпотентность приёма).
 */
#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_key', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_event_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_event_ts', columns: ['ts'])]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /** Код провайдера (ссылка на справочник неявная — события могут
     *  приходить раньше, чем провайдер попал в справочник). */
    #[ORM\Column(type: 'string', length: 64)]
    private string $provider = '';

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId = '';

    /** Код модели того же провайдера. */
    #[ORM\Column(type: 'string', length: 64)]
    private string $model = '';

    #[ORM\Column(name: 'user_code', type: 'string', length: 128)]
    private string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheWrite = 0;

    /** UsageEvent::STATUS_* */
    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_PENDING;

    /**
     * Итоговая стоимость события, округлённая ровно один раз до 6 знаков
     * (bcmath-строка). null — событие ещё не рассчитано (unrated).
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $cost = null;

    /** Строка цен, по которой событие было рассчитано. */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'price_line_id', nullable: true)]
    private ?PriceLine $priceLine = null;

    /** @param array{tokens_input:int, tokens_output:int, tokens_cache_read:int, tokens_cache_write:int} $tokens */
    public function __construct(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        array $tokens,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokens['tokens_input'];
        $this->tokensOutput = $tokens['tokens_output'];
        $this->tokensCacheRead = $tokens['tokens_cache_read'];
        $this->tokensCacheWrite = $tokens['tokens_cache_write'];
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function requestId(): string
    {
        return $this->providerRequestId;
    }

    public function key(): string
    {
        return $this->provider.'/'.$this->providerRequestId;
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

    public function tokens(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    public function cost(): ?string
    {
        return $this->cost;
    }

    /**
     * Фиксирует результат тарификации: стоимость (уже округлена до 6 знаков)
     * и строка цен, по которой посчитано.
     */
    public function rate(string $cost, PriceLine $priceLine): void
    {
        $this->status = self::STATUS_RATED;
        $this->cost = $cost;
        $this->priceLine = $priceLine;
    }

    /** Замена стоимости (ретро-коррекция цены по той же строке). */
    public function resetCost(string $cost): void
    {
        $this->cost = $cost;
    }

    public function priceLine(): ?PriceLine
    {
        return $this->priceLine;
    }
}
