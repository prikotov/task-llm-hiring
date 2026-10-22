<?php

declare(strict_types=1);

namespace App\Billing;

use App\Exception\ImportException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Оф-пиковое окно провайдера: [start, end) по времени суток UTC.
 *
 * Окно может переходить через полночь (16:30–00:30).
 * start === end трактуется как пустое окно (без скидки) — 24-часовое
 * окно со скидкой считаем нефункциональным; допущение зафиксировано
 * в PR_DESCRIPTION.md.
 *
 * Множитель хранится как десятичная строка, вычисления — bcmath.
 */
final class OffPeakWindow
{
    private const TIME_PATTERN = '/^([01]\d|2[0-3]):([0-5]\d)$/';

    public function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {
    }

    /**
     * Разбор секции `off_peak` из файла цен: null — скидки нет,
     * иначе — объект с window_start / window_end / multiplier.
     */
    public static function fromSpec(mixed $spec, string $context): ?self
    {
        if ($spec === null) {
            return null;
        }
        if (!\is_array($spec)) {
            throw new ImportException(sprintf('%s: off_peak должна быть объектом или null', $context));
        }

        $start = self::parseTime($spec['window_start'] ?? null, $context.'.window_start');
        $end = self::parseTime($spec['window_end'] ?? null, $context.'.window_end');
        $multiplier = self::parseMultiplier($spec['multiplier'] ?? null, $context.'.multiplier');

        return new self(self::toMinutes($start), self::toMinutes($end), $multiplier);
    }

    public static function toMinutes(string $hhmm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhmm, 2));

        return $h * 60 + $m;
    }

    public static function parseTime(mixed $value, string $context): string
    {
        if (!\is_string($value) || preg_match(self::TIME_PATTERN, $value) !== 1) {
            throw new ImportException(sprintf(
                '%s: ожидается время "HH:MM" (UTC), получено: %s',
                $context,
                var_export($value, true),
            ));
        }

        return $value;
    }

    private static function parseMultiplier(mixed $value, string $context): string
    {
        if (!\is_string($value) || preg_match('/^\d+(\.\d+)?$/', $value) !== 1 || bccomp($value, '0', 20) <= 0) {
            throw new ImportException(sprintf(
                '%s: ожидается десятичное число больше нуля, получено: %s',
                $context,
                var_export($value, true),
            ));
        }

        return $value;
    }

    public function startMinutes(): int
    {
        return $this->startMinutes;
    }

    public function endMinutes(): int
    {
        return $this->endMinutes;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    /**
     * Попадает ли момент в окно [start, end) по времени суток UTC.
     * ts приводится к UTC перед проверкой.
     */
    public function isInside(DateTimeImmutable $ts): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }

        $minutesOfDay = $this->minutesOfDay($ts);

        if ($this->startMinutes < $this->endMinutes) {
            return $minutesOfDay >= $this->startMinutes && $minutesOfDay < $this->endMinutes;
        }

        // Окно через полночь: [start, 24:00) ∪ [00:00, end)
        return $minutesOfDay >= $this->startMinutes || $minutesOfDay < $this->endMinutes;
    }

    private function minutesOfDay(DateTimeImmutable $ts): int
    {
        $utc = $ts->setTimezone(new DateTimeZone('UTC'));

        return ((int) $utc->format('G')) * 60 + ((int) $utc->format('i'));
    }
}
