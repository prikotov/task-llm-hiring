<?php

declare(strict_types=1);

namespace App\Service;

use App\Import\UsageEventsParser;
use App\Shared\ImportException;
use Doctrine\DBAL\Connection;

/**
 * Loads usage events with idempotent deduplication on
 * (provider, providerRequestId) — duplicate deliveries are silently ignored.
 */
final class UsageEventImporter
{
    public function __construct(
        private readonly Connection $db,
        private readonly UsageEventsParser $parser,
    ) {
    }

    /**
     * @throws ImportException on an unreadable file or an invalid line; the
     *         whole file is then rolled back (no partial state)
     *
     * @return array{lines: int, imported: int, duplicates: int}
     */
    public function importFile(string $path): array
    {
        if (!\is_file($path) || !\is_readable($path)) {
            throw ImportException::forFile($path, 'file not found or not readable');
        }

        $handle = \fopen($path, 'rb');
        if ($handle === false) {
            throw ImportException::forFile($path, 'cannot open file');
        }

        $events = [];
        $lineNo = 0;
        try {
            while (($line = \fgets($handle)) !== false) {
                ++$lineNo;
                if (\trim($line) === '') {
                    continue; // tolerate blank lines / trailing newline
                }

                $events[] = $this->parser->parseLine($line, $lineNo);
            }
        } finally {
            \fclose($handle);
        }

        $imported = 0;
        $this->db->transactional(function () use ($events, &$imported): void {
            $insert = 'INSERT INTO usage_events
                       (provider, provider_request_id, model, "user", ts,
                        tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, status)
                       VALUES
                       (:provider, :request_id, :model, :user, :ts,
                        :ti, :to, :tcr, :tcw, :status)
                       ON CONFLICT (provider, provider_request_id) DO NOTHING';

            foreach ($events as $event) {
                $imported += (int) $this->db->executeStatement(
                    $insert,
                    [
                        'provider' => $event->provider,
                        'request_id' => $event->providerRequestId,
                        'model' => $event->model,
                        'user' => $event->user,
                        'ts' => $event->ts->format('Y-m-d H:i:sO'),
                        'ti' => $event->tokensInput,
                        'to' => $event->tokensOutput,
                        'tcr' => $event->tokensCacheRead,
                        'tcw' => $event->tokensCacheWrite,
                        'status' => 'unrated',
                    ],
                );
            }
        });

        return ['lines' => \count($events), 'imported' => $imported, 'duplicates' => \count($events) - $imported];
    }
}
