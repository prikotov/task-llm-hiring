<?php

declare(strict_types=1);

namespace App\Domain\Usage;

/**
 * Жизненный цикл тарификации события.
 */
enum UsageStatus: string
{
    /** Ожидает расчёта; `app:billing:rate` повторяет попытку для всех событий в этом статусе. */
    case Pending = 'pending';

    /** Стоимость рассчитана (и, в случае ретро-коррекции, пересчитана). */
    case Rated = 'rated';
}
