<?php

declare(strict_types=1);

namespace App\Domain\Usage;

/**
 * Жизненный цикл события с точки зрения тарификации:
 *
 * pending — принято, тарификация ещё не пробовалась;
 * rated   — рассчитано, стоимость и строка цены зафиксированы;
 * unrated — пытались, подходящей строки цен нет (в отчёт не попадает;
 *           следующий запуск app:billing:rate повторит попытку).
 */
enum EventStatus: string
{
    case Pending = 'pending';
    case Rated = 'rated';
    case Unrated = 'unrated';
}
