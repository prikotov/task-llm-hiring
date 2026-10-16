<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Entity\Price;
use DateTimeImmutable;

/**
 * Выбор ставки для события (п. 1 правил тарификации):
 * строка цен с наибольшим valid_from, не превышающим момент события
 * (valid_from включительно). Если такой строки нет — событие unrated.
 */
final class PriceSelection
{
    private function __construct()
    {
    }

    /**
     * @param iterable<Price> $rows строки цен модели события
     */
    public static function best(DateTimeImmutable $eventTime, iterable $rows): ?Price
    {
        $best = null;

        foreach ($rows as $row) {
            if ($row->validFrom() > $eventTime) {
                continue; // строка ещё не действует в момент события
            }

            if ($best === null || $row->validFrom() > $best->validFrom()) {
                $best = $row;
            }
        }

        return $best;
    }
}
