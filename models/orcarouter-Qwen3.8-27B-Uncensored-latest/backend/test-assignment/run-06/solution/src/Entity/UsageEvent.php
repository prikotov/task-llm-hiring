<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (запрос к модели), доставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id); повторная доставка
 * игнорируется (идемпотентность приёма).
 */
#[ORM\Entity(repositoryClass: \App\Repository\UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_usage_provider_request', columns: ['provider', 'provider_request_id'])])]
final class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'string', length: 128)]
    public string $provider;

    #[ORM\Column(type: 'string', length: 512)]
    public string $providerRequestId;

    #[ORM\Column(type: 'string', length: 128)]
    public string $model;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    public string $user;

    /** Момент запроса (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    public int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    public int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    public int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    public int $tokensCacheWrite;

    /** unrated | rated */
    #[ORM\Column(type: 'string', length: 16)]
    public string $status = self::STATUS_UNRATED;

    /**
     * Стоимость события в микро юнитах USD (1e-6), точное целое.
     * Заполняется после тарификации; единственное округление — при расчёте.
     */
    #[ORM\Column(name: 'cost_micros', type: 'bigint', nullable: true)]
    public ?int $costMicros = null;

    /** Строка цен, по которой событие было тарифицировано (для ретро-коррекции). */
    #[ORM\Column(name: 'applied_price_id', type: 'integer', nullable: true)]
    public ?int $appliedPriceId = null;

    #[ORM\Column(name: 'rated_at', type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $ratedAt = null;
}
