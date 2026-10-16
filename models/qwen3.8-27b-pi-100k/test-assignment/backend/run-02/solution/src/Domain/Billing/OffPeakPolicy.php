<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Exception\BillingException;
use DateTimeImmutable;

/**
 * Оф-пик провайдера: окно часовых минут UTC и множитель стоимости.
 *
 * - window_start включительно, window_end исключительно;
 * - окно может переходить через полночь (16:30–00:30);
 * - равные начало и конец — вырожденное (пустое) окно, скидка не применяется;
 * - множитель — неотрицательное десятичное число (строка), применяется
 *   к стоимости всего события, попавшего в окно.
 *
 * Проверка входных данных — в конструкторе.
 */
final class OffPeakPolicy
{
    private const TIME_PATTERN = '/^([01]\d|2[0-3]):([0-5]\d)$/';
    private const DECIMAL_PATTERN = '/^\d{1,30}(?:\.\d{1,30})?$/';

    private readonly int $startMinutes;
    private readonly int $endMinutes;

    public function __construct(
        private readonly string $windowStart,
        private readonly string $windowEnd,
        private readonly string $multiplier,
    ) {
        $this->startMinutes = self::toMinutes('off_peak.window_start', $windowStart);
        $this->endMinutes = self::toMinutes('off_peak.window_end', $windowEnd);

        if (preg_match(self::DECIMAL_PATTERN, $multiplier) !== 1) {
            throw new BillingException(sprintf(
                'off_peak.multiplier должен быть неотрицательным десятичным числом, дано \'%s\'',
                $multiplier,
            ));
        }
    }

    public function windowStart(): string
    {
        return $this->windowStart;
    }

    public function windowEnd(): string
    {
        return $this->windowEnd;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    /**
     * Попадает ли время суток (UTC) данного момента в окно
     * [window_start, window_end)?
     */
    public function applies(DateTimeImmutable $utcTime): bool
    {
        $minute = (int) $utcTime->format('G') * 60 + (int) $utcTime->format('i');

        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }

        if ($this->startMinutes < $this->endMinutes) {
            return $minute >= $this->startMinutes && $minute < $this->endMinutes;
        }

        return $minute >= $this->startMinutes || $minute < $this->endMinutes;
    }

    private static function toMinutes(string $field, string $value): int
    {
        if (preg_match(self::TIME_PATTERN, $value, $matches) !== 1) {
            throw new BillingException(sprintf('%s: ожидается время в формате HH:MM, дано \'%s\'', $field, $value));
        }

        return ((int) $matches[1]) * 60 + (int) $matches[2];
    }
}
