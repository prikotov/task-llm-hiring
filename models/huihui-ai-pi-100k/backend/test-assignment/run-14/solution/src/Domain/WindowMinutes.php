<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Утилиты для работы со строками "HH:MM" (UTC) и минутами суток.
 *
 * Ввод — строка "HH:MM" (24-часовый формат UTC, минуты суток провайдера
 * задаются по clock-time, см. TASK.md: `window_start=16:30, window_end=00:30`).
 */
final class WindowMinutes
{
    /**
     * @param string $label "HH:MM" (24-hour, minutes 00..59; часы до 23 включительно)
     * @throws \DomainException
     */
    public static function from(string $label): int
    {
        $label = trim($label);
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $label, $m)) {
            throw new \DomainException(sprintf('Ожидается "HH:MM", получено: %s', $label));
        }

        $h = (int) $m[1];
        $min = (int) $m[2];
        if ($h < 0 || $h > 23 || $min < 0 || $min > 59) {
            throw new \DomainException(sprintf('Минуты суток вне диапазона 00:00..23:59: %s', $label));
        }

        return $h * 60 + $min;
    }

    /**
     * Утилитарная строка "HH:MM" из минут.
     */
    public static function label(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
