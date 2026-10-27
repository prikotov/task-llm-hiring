<?php

declare(strict_types=1);

namespace App\Domain;

use App\Entity\Provider;

/** Проверка попадания момента времени в оф-пиковое окно провайдера (UTC). */
final class OffPeakWindow
{
    private function __construct()
    {
    }

    public static function contains(Provider $provider, \DateTimeImmutable $tsUtc): bool
    {
        if (!$provider->hasOffPeak()) {
            return false;
        }

        $t = self::toMinutesOfDay($tsUtc->format('H:i'));
        $start = self::toMinutesOfDay($provider->getOffPeakWindowStart());
        $end = self::toMinutesOfDay($provider->getOffPeakWindowEnd());

        if ($start <= $end) {
            return $t >= $start && $t < $end;
        }

        // Окно через полночь: [16:30 ... 24:00) и [00:00 ... 00:30)
        return $t >= $start || $t < $end;
    }

    public static function toMinutesOfDay(string $hm): int
    {
        [$h, $m] = explode(':', $hm);

        return ((int) $h) * 60 + ((int) $m);
    }
}
