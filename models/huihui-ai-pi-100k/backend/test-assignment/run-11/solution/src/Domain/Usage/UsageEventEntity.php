<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Billing\TokenMix;
use Doctrine\ORM\Mapping as ORM;

/**
 * Импортированное (ещё не рассчитанное) событие использования.
 *
 * Естественный ключ — (provider, provider_request_id); идемпотентная запись
 * по нему (TASK.md п. 5).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_key', columns: ['provider', 'provider_request_id'])]
class UsageEventEntity
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
}
