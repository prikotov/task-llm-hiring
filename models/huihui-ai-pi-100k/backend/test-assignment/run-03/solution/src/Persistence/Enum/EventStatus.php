<?php

declare(strict_types=1);

namespace App\Persistence\Enum;

/**
 * Состояние события в тарификационном цикле:
 *  pending — импортировано, ещё не проходило попытку расчёта;
 *  unrated — попытка сделана, подходящей строки цен нет (повторяется при каждом запуске);
 *  rated — стоимость рассчитана и зафиксирована.
 */
enum EventStatus: string
{
    case Pending = 'pending';
    case Unrated = 'unrated';
    case Rated = 'rated';
}
