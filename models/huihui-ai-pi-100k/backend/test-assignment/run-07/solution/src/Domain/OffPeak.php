<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Оф-пиковая скидка провайдера — фиксированное окно по времени суток UTC.
 *
 * Правило попадания события в окно: `[window_start, window_end)`:
 * `window_start` включительно, `window_end` исключительно. Окно может
 * переходить через полночь (например, `16:30`–`00:30`). Сравнение ведётся
 * по секундам суток; событие в окне удваивает стоимость всего события
 * множителем `multiplier`.
 */
final class OffPeak
{
    private const TIME_PATTERN = '/^([01]\d|2[0-3]):([0-5]\d)$/' ;

    /**
     * @param string $multiplier десятичная строка (например, "0.5")
     */
    private function __construct(
        private readonly string $windowStart,
        private readonly string $windowEnd,
        private readonly string $multiplier,
    ) {
    }

    /**
     * Создаёт скидку из трёх полей справочника провайдера.
     * Возвращает null, если оф-пик не задан (любой из полей null).
     */
    public static function fromProvider(string|float|null $start, string|float|null $end, string|float|null $multiplier): ?self
    {
        if ($start === null || $end === null || $multiplier === null) {
            return null;
        }

        return new self((string) $start, (string) $end, (string) $multiplier);
    }

    /**
     * Применяется ли скидка к событию с указанным временем UTC.
     */
    public function appliesAtUtc(\DateTimeInterface $tsUtc): bool
    {
        $s = self::toSeconds($this->windowStart);
        $e = self::toSeconds($this->windowEnd);
        $t = self::toSecondsDay($tsUtc);

        if ($s === $e) {
            return false;
        }

        // Окно без перехода через полночь.
        if ($s < $e) {
            return $t >= $s && $t < $e;
        }

        // Окно с переходом через полночь: [s, 24:00) ∪ [00:00, e).
        return $t >= $s || $t < $e;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    private static function toSeconds(string $hhmm): int
    {
        if (!preg_match(self::TIME_PATTERN, $hhmm, $m)) {
            throw new \ValueError(sprintf('Некорректное время оф-пик: "%s" (ожидался ХХ:ММ по UTC)', $hhmm));
        }

        return ((int) $m[1]) * 3600 + ((int) $m[2]) * 60;
    }

    private static function toSecondsDay(\DateTimeInterface $ts): int
    {
        return ((int) $ts->format('H')) * 3600 + ((int) $ts->format('i')) * 60 + (int) $ts->format('s');
    }
}
