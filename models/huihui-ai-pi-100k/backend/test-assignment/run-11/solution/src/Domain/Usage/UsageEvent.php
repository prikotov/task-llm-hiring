<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Billing\TokenMix;

/**
 * Событие использования из журнала провайдера (импорт, ещё не рассчитано).
 *
 * Натуральный ключ — (provider, provider_request_id).
 *
 * @readonly
 */
final class UsageEvent
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts,
        public readonly TokenMix $tokens,
    ) {
    }
}
