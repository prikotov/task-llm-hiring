<?php

declare(strict_types=1);

namespace App\Domain;

/** Состояние события в процессе тарификации. */
enum EventStatus: string
{
    /** Ещё не участвовало в запуске app:billing:rate. */
    case Pending = 'pending';

    /** Отнесено к строке цен, стоимость посчитана. */
    case Rated = 'rated';

    /** В момент последней попытки строки цен не найдена;
     * следующие запуски rate повторят попытку. */
    case Unrated = 'unrated';
}
