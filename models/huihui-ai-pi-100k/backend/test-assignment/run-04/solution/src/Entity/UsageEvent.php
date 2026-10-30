<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events', columns: ['provider_code', 'provider_request_id'])]
final class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        #[ORM\Column(type: Types::BIGINT)]
        public int $id,

        #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 128)]
        public string $providerCode,

        /** Естественный ключ события (вместе с провайдером); источник дедупликации. */
        #[ORM\Column(name: 'provider_request_id', type: Types::STRING, length: 255)]
        public string $providerRequestId,

        #[ORM\Column(name: 'model_code', type: Types::STRING, length: 128)]
        public string $modelCode,

        #[ORM\Column(name: 'user_id', type: Types::STRING, length: 255)]
        public string $userId,

        /** Момент запроса, UTC. */
        #[ORM\Column(name: 'ts', type: Types::DATETIME_UTC_IMMUTABLE)]
        public DateTimeImmutable $ts,

        #[ORM\Column(name: 'tokens_input', type: Types::BIGINT)]
        public int $tokensInput = 0,

        #[ORM\Column(name: 'tokens_output', type: Types::BIGINT)]
        public int $tokensOutput = 0,

        #[ORM\Column(name: 'tokens_cache_read', type: Types::BIGINT)]
        public int $tokensCacheRead = 0,

        #[ORM\Column(name: 'tokens_cache_write', type: Types::BIGINT)]
        public int $tokensCacheWrite = 0,

        #[ORM\Column(name: 'status', type: Types::STRING, length: 16)]
        public string $status = self::STATUS_UNRATED,

        /** Итоговая стоимость события (USD), округлёнa HALF_UP до 6 знаков; null, пока unrated. */
        #[ORM\Column(name: 'cost_usd', type: Types::DECIMAL, nullable: true, options: ['precision' => 24, 'scale' => 6])]
        public ?string $cost = null,

        /** Id строки цен, по которой событие рассчитано (след для reprice/аудита). */
        #[ORM\Column(name: 'price_row_id', type: Types::BIGINT, nullable: true)]
        public ?int $priceRowId = null,
    ) {
    }
}
