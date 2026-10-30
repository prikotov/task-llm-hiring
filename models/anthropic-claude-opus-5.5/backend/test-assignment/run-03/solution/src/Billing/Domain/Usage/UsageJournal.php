<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

use App\Billing\Domain\Model\ModelReference;

/**
 * Порт журнала событий использования.
 *
 * Домен отдаёт и принимает только доменные события; реализация сама
 * сопоставляет их персистентным записям (событие идентифицируется id).
 */
interface UsageJournal
{
    /**
     * Добавляет событие, если его ещё нет (дедупликация по п. 5).
     *
     * @return bool true — добавлено, false — дубликат
     */
    public function addIfAbsent(UsageEvent $event): bool;

    /**
     * События, ещё не прошедшие тарификацию (включая ранее unrated).
     *
     * @return list<UsageEvent>
     */
    public function unrated(): array;

    /**
     * События, тарифицированные конкретной строкой цен.
     *
     * @return list<UsageEvent>
     */
    public function ratedByPriceRow(int $priceRowId): array;

    /**
     * Сохраняет обновлённое состояние уже существующих событий пачкой.
     *
     * @param list<UsageEvent> $events
     */
    public function saveRating(array $events): void;

    /**
     * @param list<CostAdjustment> $adjustments
     */
    public function recordAdjustments(array $adjustments): void;

    public function all(): iterable;
}
