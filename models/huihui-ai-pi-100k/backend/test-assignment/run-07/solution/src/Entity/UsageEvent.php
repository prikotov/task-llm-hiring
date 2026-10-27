<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования — факт расходования токенов, доставленный провайдером.
 *
 * Естественный ключ события — `(provider, providerRequestId)`: повторная
 * доставка уже известного события дедуплицируется (ON CONFLICT DO NOTHING),
 * двойного списания не возникает.
 *
 * Жизненный цикл: `pending` -> (app:billing:rate) -> `rated`.
 * `costUsd` — итоговая стоимость события, округлённая до 6 знаков
 * (десятичная строка), заполняется только при rated.
 * `priceValidFrom` — `valid_from` строки цен, которой тарифицировано событие.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(
    name: 'uq_usage_events_provider_request',
    columns: ['provider_code', 'provider_request_id'],
)]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_events_reprice', columns: ['provider_code', 'model_code', 'price_valid_from'])]
final class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: Types::BIGINT)]
        public int $id = 0,

        #[ORM\Column(type: Types::STRING, length: 64)]
        public string $providerCode,

        #[ORM\Column(type: Types::STRING, length: 255)]
        public string $providerRequestId,

        public string $modelCode,

        public string $userId,

        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
        public \DateTimeImmutable $ts,

        #[ORM\Column(type: Types::BIGINT)]
        public int $tokensInput = 0,

        #[ORM\Column(type: Types::BIGINT)]
        public int $tokensOutput = 0,

        #[ORM\Column(type: Types::BIGINT)]
        public int $tokensCacheRead = 0,

        #[ORM\Column(type: Types::BIGINT)]
        public int $tokensCacheWrite = 0,

        #[ORM\Column(type: Types::STRING, length: 16)]
        public string $status = self::STATUS_PENDING,

        #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
        public ?string $costUsd = null,

        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
        public ?\DateTimeImmutable $priceValidFrom = null,
    ) {
    }
}
