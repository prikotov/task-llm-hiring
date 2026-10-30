<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;

/**
 * Выбор строки цены для события: наибольшее valid_from <= ts события
 * (valid_from включительно). Строк нет — null ⇒ событие unrated.
 */
final class PriceRowSelector
{
    /**
     * @param list<PriceRow> $rows все строки цен модели (в любом порядке)
     */
    public function select(array $rows, \DateTimeImmutable $eventTs): ?PriceRow
    {
        $best = null;

        foreach ($rows as $row) {
            if ($row->getValidFrom() > $eventTs) {
                continue;
            }

            if ($best === null || $row->getValidFrom() > $best->getValidFrom()) {
                $best = $row;
            }
        }

        return $best;
    }
}
