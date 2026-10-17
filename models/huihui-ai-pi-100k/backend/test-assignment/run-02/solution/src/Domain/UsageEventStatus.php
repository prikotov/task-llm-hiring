<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Состояние события использования с точки зрения тарификации.
 *
 * Unrated — событие ещё не тарифицировано (импорт) либо попытка
 * тарификации не удалась из-за отсутствия цены. Такие события
 * каждый запуск app:billing:rate пробует заново.
 * Rated — событие рассчитано, стоимость зафиксирована.
 */
enum UsageEventStatus: string
{
    case Unrated = 'unrated';
    case Rated = 'rated';
}
