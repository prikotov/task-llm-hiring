<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use function ctype_digit;
use function explode;

/**
 * Оф-пиковая скидка провайдера: окно времени суток UTC, в котором
 * стоимость события умножается на multiplier.
 *
 * Окно [start, end): start включительно, end исключительно.
 * Окно может переходить через полночь (например, 16:30–00:30).
 * Совпадение границ трактуется как «окно на весь день».
 */
final class OffPeak
{
    private function __construct(
        private readonly int $windowStartSeconds,
        private readonly int $windowEndSeconds,
        public readonly string $multiplier,
    ) {
    }

    /**
     * Создаёт политику из полей справочника; null — скидки нет.
     *
     * @throws \InvalidArgumentException при неверном формате границ окна
     */
    public static function fromWindow(
        ?string $startHHMM,
        ?string $endHHMM,
        ?string $multiplier,
    ): ?self {
        if ($startHHMM === null || $endHHMM === null || $multiplier === null) {
            return null;
        }

        return new self(
            self::toDaySeconds($startHHMM),
            self::toDaySeconds($endHHMM),
            $multiplier,
        );
    }

    /** Проверяет, попадает ли момент времени (UTC) в окно скидки. */
    public function isActiveAt(\DateTimeImmutable $utc): bool
    {
        $t = ((int) $utc->format('H')) * 3600
            + ((int) $utc->format('i')) * 60
            + ((int) $utc->format('s'));

        $start = $this->windowStartSeconds;
        $end = $this->windowEndSeconds;

        if ($start === $end) {
            return true; // окно на весь день
        }

        if ($start < $end) {
            return $t >= $start && $t < $end;
        }

        // окно пересекает полночь: [start, 24:00) ∪ [00:00, end)
        return $t >= $start || $t < $end;
    }

    private static function toDaySeconds(string $hhmm): int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $hhmm, $m)) {
            throw new \InvalidArgumentException(
                sprintf('Некорректное время окна скидок: "%s" (ожидался формат ЧЧ:ММ)', $hhmm)
            );
        }

        $hours = (int) $m[1];
        $minutes = (int) $m[2];
        if ($hours > 23 || $minutes > 59) {
            throw new \InvalidArgumentException(
                sprintf('Некорректное время окна скидок: "%s"', $hhmm)
            );
        }
        if (!ctype_digit($m[1]) || !ctype_digit($m[2])) {
            throw new \InvalidArgumentException(
                sprintf('Некорректное время окна скидок: "%s"', $hhmm)
            );
        }

        return $hours * 3600 + $minutes * 60;
    }
}
