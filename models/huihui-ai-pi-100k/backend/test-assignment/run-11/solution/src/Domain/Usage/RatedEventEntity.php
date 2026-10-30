<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Billing\Cost;
use App\Domain\Billing\TokenMix;
use Doctrine\ORM\Mapping as ORM;

/**
 * Результат тарификации события (текущая стоимость).
 *
 * Естественный ключ — (provider, provider_request_id) (TASK.md п. 5).
 * Идемпотентность: повторная запись с тем же ключом — ошибка (уникальность).
 *
 * При reprice (бонус) стоимость НЕ заменяется, а корректируется:
 *  - `costUsd`         — текущая (округлённая) стоимость;
 *  - `originalCostUsd` — стоимость первого расчёта (аудит);
 *  - `repricedCount`   — число применённых reprice-корректировок.
 */
#[ORM\Entity]
#[ORM\Table(name: 'rated_events')]
#[ORM\UniqueConstraint(name: 'uq_rated_events_key', columns: ['provider', 'provider_request_id'])]
class RatedEventEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $providerRequestId = '';

    #[ORM\Column(type: 'string', length: 64)]
    public string $model = '';

    #[ORM\Column(type: 'string', length: 64, name: 'usr')]
    public string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheWrite = 0;

    /**
     * @var string decimal — текущая (округлённая) стоимость в USD
     */
    #[ORM\Column(type: 'string', length: 40)]
    public string $costUsd = '0.000000';

    /**
     * @var string|null decimal — стоимость первого расчёта (аудит)
     */
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $originalCostUsd = null;

    public int $repricedCount = 0;

    public function toDomain(): UsageEvent
    {
        return new UsageEvent(
            provider: $this->provider,
            providerRequestId: $this->providerRequestId,
            model: $this->model,
            user: $this->user,
            ts: $this->ts,
            tokens: new TokenMix(
                $this->tokensInput,
                $this->tokensOutput,
                $this->tokensCacheRead,
                $this->tokensCacheWrite,
            ),
        );
    }

    public function cost(): Cost
    {
        return Cost::make($this->costUsd);
    }
}
