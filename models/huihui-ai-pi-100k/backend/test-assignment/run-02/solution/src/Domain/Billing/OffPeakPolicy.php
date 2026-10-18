<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Политика оф-пик провайдера: окно [start, end) по минутам суток UTC
 * (может пересекать полночь) и множитель стоимости.
 */
final class OffPeakPolicy
{
    public function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {
    }

    /**
     * Попадает ли минута суток в окно скидки [start, end), включая
     * окно, пересекающее полночь (например 16:30–00:30).
     *
     * Дегенерированное окно (start == end) считается пустым.
     */
    public function applies(int $minuteOfDay): bool
    {
        $start = $this->startMinutes;
        $end = $this->endMinutes;

        if ($start === $end) {
            return false;
        }
        if ($start < $end) {
            return $minuteOfDay >= $start && $minuteOfDay < $end;
        }

        return $minuteOfDay >= $start || $minuteOfDay < $end;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }
}
