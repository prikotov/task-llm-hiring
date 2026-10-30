<?php

declare(strict_types=1);

namespace App\Service\Billing;

use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\PriceCandidate;
use App\Domain\Billing\PriceUnit;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка кандидатов на тарификацию событий: строки цен модели события,
 * действовавших в момент события (valid_from <= ts), упорядоченные по
 * valid_from DESC — первый элемент для события — строка с наибольшим
 * valid_from <= ts события.
 */
final class PriceCandidateLoader
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @param list<int> $eventIds
     *
     * @return array<int, list<PriceCandidate>> карта: event id => кандидаты (по убыванию valid_from)
     */
    public function loadForEvents(array $eventIds): array
    {
        if ($eventIds === []) {
            return [];
        }

        $placeholders = implode(',', array_map('\strval', $eventIds));
        $rows = $this->em->getConnection()->fetchAllAssociative(
            "SELECT e.id AS event_id,
                    p.id AS price_id,
                    p.valid_from AS valid_from,
                    p.unit AS unit,
                    p.rates AS rates,
                    pr.off_peak_window_start AS op_start,
                    pr.off_peak_window_end AS op_end,
                    pr.off_peak_multiplier AS op_multiplier
             FROM usage_events e
             JOIN providers pr ON pr.code = e.provider
             JOIN models m ON m.provider_id = pr.id AND m.code = e.model
             JOIN price_rows p ON p.model_id = m.id AND p.valid_from <= e.ts
             WHERE e.id IN ($placeholders)
             ORDER BY e.id ASC, p.valid_from DESC",
        );

        $result = [];
        foreach ($rows as $row) {
            $offPeak = null;
            if ($row['op_start'] !== null && $row['op_multiplier'] !== null) {
                $offPeak = new OffPeakWindow((string) $row['op_start'], (string) $row['op_end'], (string) $row['op_multiplier']);
            }

            $result[(int) $row['event_id']][] = new PriceCandidate(
                (int) $row['price_id'],
                new \DateTimeImmutable($row['valid_from'], new \DateTimeZone('UTC')),
                PriceUnit::fromString((string) $row['unit']),
                json_decode((string) $row['rates'], true, 512, JSON_THROW_ON_ERROR),
                $offPeak,
            );
        }

        ksort($result);

        return $result;
    }
}
