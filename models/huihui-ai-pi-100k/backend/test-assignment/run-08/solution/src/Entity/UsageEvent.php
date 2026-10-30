<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (журнал). Натуральный ключ —
 * (provider_code, provider_request_id); дедупликация по нему.
 *
 * status:
 *  - «pending»: ещё не расценено (unrated; цена могла подтянуться позже);
 *  - «rated»:   стоимость зафиксирована, округлена до 6 знаков.
 *
 * costUsd / ставки провайдера — строки (bcmath), float не используется.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_events_reprice', columns: ['provider_code', 'model_code', 'price_valid_from'])]
final class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    public string $providerRequestId = '';

    #[ORM\Column(name: 'model_code', type: 'string', length: 64)]
    public string $modelCode = '';

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    public string $userId = '';

    /** Мгновение события (UTC, «Y-m-d H:i:sO»). */
    #[ORM\Column(type: 'tz_date_string')]
    public string $ts = '';

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 16, options: ['default' => 'pending'])]
    public string $status = self::STATUS_PENDING;

    /** Строка-число, ровно 6 знаков; null у unrated-событий. */
    #[ORM\Column(name: 'cost_usd', type: 'string', length: 30, nullable: true)]
    public ?string $costUsd = null;

    /** valid_from строки цен, по которой расценено событие (UTC, «Y-m-d H:i:sO»). */
    #[ORM\Column(name: 'price_valid_from', type: 'tz_date_string', nullable: true)]
    public ?string $priceValidFrom = null;

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    /** @return array{input:int, output:int, cache_read:int, cache_write:int} */
    public function tokenCounts(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
    }

    public function markRated(string $priceValidFrom, string $costUsd): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceValidFrom = $priceValidFrom;
        $this->costUsd = $costUsd;
    }
}
