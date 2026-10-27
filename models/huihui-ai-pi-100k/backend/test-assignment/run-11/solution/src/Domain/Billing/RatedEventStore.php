<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Usage\UsageEvent;

/**
 * Порт: хранение результатов тарификации.
 *
 * Реализация гарантирует идемпотентную запись по естественному ключу
 * (provider, provider_request_id): повторная доставка не обновляет и
 * не доначисляет (TASK.md п. 5).
 */
interface RatedEventStore
{
    public function saveRated(UsageEvent $event, Cost $cost): void;
}
