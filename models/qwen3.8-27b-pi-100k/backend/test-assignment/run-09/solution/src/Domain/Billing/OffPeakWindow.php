<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Entity\Provider;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Оф-пиковое окно провайдера (UTC), заданное временем суток:
 * [window_start, window_end), может переходить через полночь.
 *
 * window_start включительно, window_end исключительно.
 */
final readonly class OffPeakWindow
{
    /** @param string $multiplier десятичный множитель стоимости (например "0.5") */
    private function __construct(
        public int $startMinutes,
        public int $endMinutes,
        public string $multiplier,
    ) {
    }

    /** Создание по кодам окна "HH:MM"; окно считается активным, задано. */
    public static function fromCodes(string $startCode, string $endCode, string $multiplier): self
    {
        return new self(
            self::parseMinutes($startCode),
            self::parseMinutes($endCode),
            $multiplier,
        );
    }

    /**
     * Возвращает окно провайдера или null, если у него оф-пик не задан.
     */
    public static function fromProvider(Provider $provider): self|null
    {
        $start = $provider->getOffPeakWindowStart();
        $end = $provider->getOffPeakWindowEnd();
        $multiplier = $provider->getOffPeakMultiplier();

        if (null === $start || null === $end || null === $multiplier) {
            return null;
        }

        return self::fromCodes($start, $end, $multiplier);
    }

    /**
     * Попадает ли время события (UTC) в окно [start, end)?
     */
    public function covers(DateTimeImmutable $eventTs): bool
    {
        $t = self::minutesOfUtc($eventTs);

        if ($this->startMinutes === $this->endMinutes) {
            return false; // вырожденное окно — считаем, что скидки нет
        }

        if ($this->startMinutes < $this->endMinutes) {
            return $t >= $this->startMinutes && $t < $this->endMinutes;
        }

        // Окно через полночь: t в [start, 24:00) либо t в [00:00, end)
        return $t >= $this->startMinutes || $t < $this->endMinutes;
    }

    public static function parseMinutes(string $code): int
    {
        if (!preg_match('/^\d{1,2}:\d{2}$/', $code)) {
            throw new \DomainException(sprintf('Некорректное время окна: "%s"', $code));
        }

        [$h, $m] = array_map('intval', explode(':', $code, 2));

        if ($h > 23 || $m > 59) {
            throw new \DomainException(sprintf('Некорректное время окна: "%s"', $code));
        }

        return $h * 60 + $m;
    }

    private static function minutesOfUtc(DateTimeImmutable $ts): int
    {
        $utc = $ts->setTimezone(new DateTimeZone('UTC'));

        return (int) $utc->format('G') * 60 + (int) $utc->format('i');
    }
}
