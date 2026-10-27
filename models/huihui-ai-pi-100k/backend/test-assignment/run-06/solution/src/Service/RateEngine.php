<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use Doctrine\DBAL\Connection;

final class RateEngine
{
    public function __construct(
        private readonly Connection $db,
        private readonly BillingEngine $billing
    ) {
    }

    /**
     * Rate all pending and previously unrated events.
     *
     * @return array{rated: int, unrated: int, unrated_events: array}
     */
    public function rateAll(): array
    {
        $rows = $this->db->fetchAllAssociative(
            "SELECT id, provider_code, model_code, user_id, ts,
                    provider_request_id,
                    tokens_input, tokens_output, tokens_cache_read, tokens_cache_write
             FROM usage_events
             WHERE status IN ('pending', 'unrated')
             ORDER BY id"
        );

        $rated = 0;
        $unrated = 0;
        $unratedEvents = [];

        foreach ($rows as $event) {
            $tokens = [
                'input' => (int) $event['tokens_input'],
                'output' => (int) $event['tokens_output'],
                'cache_read' => (int) $event['tokens_cache_read'],
                'cache_write' => (int) $event['tokens_cache_write'],
            ];

            [$cost, $priceValidFrom] = $this->billing->computeCost(
                $event['provider_code'],
                $event['model_code'],
                $event['ts'],
                $tokens
            );

            if ($cost !== null) {
                $this->db->executeStatement(
                    'UPDATE usage_events SET status = ?, cost = ?, price_valid_from = ?::timestamptz WHERE id = ?',
                    ['rated', $cost, $priceValidFrom, $event['id']]
                );
                $rated++;
            } else {
                $this->db->executeStatement(
                    'UPDATE usage_events SET status = ?, cost = NULL, price_valid_from = NULL WHERE id = ?',
                    ['unrated', $event['id']]
                );
                $unrated++;
                $unratedEvents[] = [
                    'provider' => $event['provider_code'],
                    'id' => $event['provider_request_id'],
                    'ts' => (new \DateTimeImmutable($event['ts'], new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z'),
                ];
            }
        }

        return ['rated' => $rated, 'unrated' => $unrated, 'unrated_events' => $unratedEvents];
    }
}
