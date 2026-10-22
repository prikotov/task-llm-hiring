<?php

declare(strict_types=1);

namespace App\Domain;

enum EventStatus: string
{
    /** Событие получено, но пока не найдена применимая строка цен. */
    case Unrated = 'unrated';

    /** Событие рассчитано: стоимость зафиксирована. */
    case Rated = 'rated';
}
