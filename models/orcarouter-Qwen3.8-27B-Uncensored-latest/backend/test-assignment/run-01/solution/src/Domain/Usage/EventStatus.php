<?php

declare(strict_types=1);

namespace App\Domain\Usage;

enum EventStatus: string
{
    /** Цена ещё не найдена (или не подбирали) — событие будет перепробовано при следующем `billing:rate`. */
    case Pending = 'pending';
    /** Стоимость рассчитана и сохранена. */
    case Rated = 'rated';
}
