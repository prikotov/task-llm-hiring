<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования, доставленное провайдером.
 * Естественный ключ — (provider, providerRequestId); дубли доставки игнорируются.
 *
 * provider/model хранятся строками кодов (ссылок через внешний ключ нет:
 * провайдеры присылают события независимо от состояния справочника;
 * нетарифицируемые события остаются pending, пока цены не появятся).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_key', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status_ts', columns: ['status', 'ts'])]
#[ORM\Index(name: 'idx_usage_events_price_row_id', columns: ['price_row_id'])]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Column(type: 'string', length: 190)]
    public string $providerRequestId = '';

    #[ORM\Column(type: 'string', length: 128)]
    public string $model = '';

    #[ORM\Column(name: 'user_ref', type: 'string', length: 190)]
    public string $user = '';

    /// Время запроса (UTC)
    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'bigint')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'bigint')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'bigint')]
    public int $tokensCacheWrite = 0;

    /// @see self::STATUS_*
    #[ORM\Column(type: 'string', length: 16)]
    public string $status = self::STATUS_PENDING;

    /** Строка цен, по которой рассчитано событие (для reprice). */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $priceRowId = null;

    /**
     * Итоговая стоимость события, USD, точно 6 знаков (десятичная строка, без float).
     * Устанавливается один раз при тарификации; при reprice — перезаписывается.
     */
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $costUsd = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $ratedAt = null;

    /**
     * @param list<int> $tokens [input, output, cache_read, cache_write]
     */
    public function __construct(string $provider, string $providerRequestId, string $model, string $user, \DateTimeImmutable $ts, int $tokensInput = 0, int $tokensOutput = 0, int $tokensCacheRead = 0, int $tokensCacheWrite = 0)
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

    public function markRated(int $priceRowId, string $costUsd, ?\DateTimeImmutable $now = null): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRowId = $priceRowId;
        $this->costUsd = $costUsd;
        $this->ratedAt = $now ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}
