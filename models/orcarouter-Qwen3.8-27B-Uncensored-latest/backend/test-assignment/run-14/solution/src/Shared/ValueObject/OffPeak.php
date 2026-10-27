<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

/**
 * Off-peak window of a provider (UTC time-of-day, minutes since midnight).
 */
final class OffPeak
{
    /**
     * @param array{startMin: int, endMin: int, multiplier: string} $window
     *        (multiplier accepted as a plain decimal string)
     * @param string $tsIso canonical "YYYY-MM-DDTHH:MM:SS+00:00"
     */
    public static function activeAt(array $window, string $tsIso): bool
    {
        $mins = ((int) substr($tsIso, 11, 2)) * 60 + (int) substr($tsIso, 14, 2);
        $start = (int) $window['startMin'];
        $end   = (int) $window['endMin'];

        if ($start < $end) {
            return $mins >= $start && $mins < $end;
        }

        // window crosses midnight, e.g. 16:30 → 00:30
        return $mins >= $start || $mins < $end;
    }
}
