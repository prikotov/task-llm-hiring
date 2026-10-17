<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнал, иммутабельное после импорта).
 * Естественный ключ события — (provider_code, provider_request_id).
 *
 * status: 'unrated' → рассчитанных нет ещё (в т.ч. пока нет строки цен);
 *        'rated' → cost заполнен (округлён до 6 знаков, точное десятичное значение).
 * price_line_id — строка цен, на которую фактически рассчитано событие
 * (нужна для ретро-коррекции — app:billing:reprice).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider_code', 'provider_request_id'])]
class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 128)]
    public string $providerCode = '';

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    public string $providerRequestId = '';

    #[ORM\Column(name: 'model_code', type: 'string', length: 128)]
    public string $modelCode = '';

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    public string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    public int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    public int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 16)]
    public string $status = self::STATUS_UNRATED;

    /** Точная десятичная строка, например "14.262499". */
    #[ORM\Column(type: 'decimal', precision: 28, scale: 6, nullable: true)]
    public ?string $cost = null;

    #[ORM\Column(name: 'price_line_id', type: 'integer', nullable: true)]
    public ?int $priceLineId = null;

    #[ORM\Column(name: 'rated_at', type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $ratedAt = null;
}
