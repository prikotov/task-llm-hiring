<?php

declare(strict_types=1);

namespace App\Domain\Usage;

/**
 * Порт: журнал импортированных событий использования, которые ещё не
 * обработаны тарификатором.
 *
 * Идемпотентность при импорте — на стороне импорта
 * (dedup by (provider, provider_request_id), see UsageEventRepository).
 */
interface UsageEventRepository
{
    /** @return UsageEvent[] */
    public function findUnrated(): array;
}
