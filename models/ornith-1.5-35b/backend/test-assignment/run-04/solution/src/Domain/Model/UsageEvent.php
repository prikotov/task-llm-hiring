<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Импортированное событие использования.
 *
 * (provider, provider_request_id) — естественный уникальный ключ дедупликации.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\Index(name: 'ev_user_ts', columns: ['user', 'event_ts'])]
class UsageEvent
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $providerCode,

        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $providerRequestId,

        #[ORM\Column(type: 'string')]
        public readonly string $modelCode,

        #[ORM\Column(type: 'string')]
        public readonly string $user,

        #[ORM\Column(type: 'datetime_immutable')]
        public readonly \DateTimeImmutable $eventTs,

        #[ORM\Column(type: 'integer')]
        public readonly int $tokensInput,

        #[ORM\Column(type: 'integer')]
        public readonly int $tokensOutput,

        #[ORM\Column(type: 'integer')]
        public readonly int $tokensCacheRead,

        #[ORM\Column(type: 'integer')]
        public readonly int $tokensCacheWrite,
    ) {
    }

    public function getAnyTokens(): int
    {
        return $this->tokensInput + $this->tokensOutput + $this->tokensCacheRead + $this->tokensCacheWrite;
    }
}
