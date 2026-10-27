<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\UsageRecord;
use App\Entity\UsageEvent;
use App\Import\InputFormatException;
use App\Import\InputParser;

/**
 * Приём журнала событий по правилу идемпотентности:
 * дедупликация по (provider, provider_request_id);
 * повторная доставка уже известного события — целиком игнорируется.
 */
final class UsageIngestService
{
    public function __construct(private readonly \Doctrine\ORM\EntityManagerInterface $em)
    {
    }

    /**
     * @return array{inserted:int, duplicates:int}
     * @throws InputFormatException при некорректной строке (transaction откатывается)
     */
    public function import(string $file): array
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new InputFormatException(sprintf('File not found or unreadable: %s', $file));
        }

        $fh = fopen($file, 'rb');
        if ($fh === false) {
            throw new InputFormatException(sprintf('Cannot open file: %s', $file));
        }

        try {
            $lines = [];
            $lineNo = 0;
            while (($line = fgets($fh)) !== false) {
                $lineNo++;
                $line = rtrim($line, "\r\n");
                if (trim($line) === '') {
                    continue;
                }
                $lines[] = [$lineNo, trim($line)];
            }

            $em = $this->em;
            return $em->wrapInTransaction(function () use ($lines, $em): array {
                [$inserted, $duplicates] = [0, 0];
                $conn = $em->getConnection();

                foreach ($lines as [$lineNo, $json]) {
                    $row = InputParser::usageRecord($json, $lineNo);
                    $record = UsageRecord::fromParsed($row);

                    $sql = '
                        INSERT INTO usage_events
                            (provider_code, provider_request_id, model_code, user_id, ts,
                             tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, status)
                        VALUES (:provider, :request_id, :model, :user, :ts,
                                :t_in, :t_out, :t_cr, :t_cw, :status)
                        ON CONFLICT (provider_code, provider_request_id) DO NOTHING
                    ';
                    $affected = $conn->executeStatement($sql, [
                        'provider' => $record->provider,
                        'request_id' => $record->providerRequestId,
                        'model' => $record->model,
                        'user' => $record->user,
                        'ts' => $record->ts->format('Y-m-d H:i:sO'),
                        't_in' => $record->tokensInput,
                        't_out' => $record->tokensOutput,
                        't_cr' => $record->tokensCacheRead,
                        't_cw' => $record->tokensCacheWrite,
                        'status' => UsageEvent::STATUS_PENDING,
                    ]);

                    if ($affected === 1) {
                        $inserted++;
                    } else {
                        $duplicates++;
                    }
                }

                return ['inserted' => $inserted, 'duplicates' => $duplicates];
            });
        } finally {
            fclose($fh);
        }
    }
}
