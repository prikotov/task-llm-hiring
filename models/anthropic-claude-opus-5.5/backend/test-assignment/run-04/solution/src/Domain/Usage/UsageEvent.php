<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use DateTimeImmutable;

/**
 * Событие использования. Естественный ключ — (providerCode, providerRequestId)
 * на стороне провайдера.
 */
final readonly class UsageEvent
{
    public function __construct(
        public string $providerCode,
        public string $providerRequestId,
        public string $modelCode,
        public string $userRef,
        public DateTimeImmutable $occurredAt,
        public TokenUsage $tokens,
        public ?int $id = null,
    ) {
        if ('' === $providerCode || '' === $providerRequestId) {
            throw new \InvalidArgumentException('Ключ события (provider, provider_request_id) обязателен.');
        }
        if ('' === $modelCode) {
            throw new \InvalidArgumentException('Код модели в событии не может быть пустым.');
        }
    }

    public function key(): string
    {
        return $this->providerCode . "\0" . $this->providerRequestId;
    }
}
