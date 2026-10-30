<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\ImportDataException;
use App\Domain\UsageEvent;
use App\Repository\DomainRepository;

/**
 * Импорт событий использования (JSONL).
 *
 * Идемпотентность — по ключу (provider, provider_request_id):
 * повторная доставка уже известного события игнорируется.
 */
final class UsageImportService
{
    public function __construct(
        private readonly DomainRepository $repo,
    ) {}

    public function import(string $path): int
    {
        if (!is_file($path) || !is_readable($path)) {
            throw ImportDataException::new("Cannot read file '{$path}'");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw ImportDataException::new("Cannot open file '{$path}'");
        }

        $imported = 0;
        $lineNo = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }
            $lineNo++;

            $data = json_decode($line, true);

            if (!is_array($data)) {
                throw ImportDataException::new("Line {$lineNo}: invalid JSONL line");
            }

            $providerCode = $data['provider'] ?? null;
            $providerRequestId = $data['provider_request_id'] ?? null;
            $modelCode = $data['model'] ?? null;
            $user = $data['user'] ?? null;
            $tsRaw = $data['ts'] ?? null;

            if (!is_string($providerCode) || $providerCode === '') {
                throw ImportDataException::new("Line {$lineNo}: missing 'provider'");
            }
            if (!is_string($providerRequestId) || $providerRequestId === '') {
                throw ImportDataException::new("Line {$lineNo}: missing 'provider_request_id'");
            }
            if (!is_string($modelCode) || $modelCode === '') {
                throw ImportDataException::new("Line {$lineNo}: missing 'model'");
            }
            if (!is_string($user) || $user === '') {
                throw ImportDataException::new("Line {$lineNo}: missing 'user'");
            }
            if (!is_string($tsRaw) || $tsRaw === '') {
                throw ImportDataException::new("Line {$lineNo}: missing 'ts'");
            }

            $exists = $this->repo->findUsageEvent($providerCode, $providerRequestId);

            if ($exists !== null) {
                continue;
            }

            try {
                $ts = new \DateTimeImmutable($tsRaw);
            } catch (\Exception) {
                throw ImportDataException::new("Line {$lineNo}: unparseable timestamp '{$tsRaw}'");
            }

            $event = new UsageEvent(
                providerCode: $providerCode,
                providerRequestId: $providerRequestId,
                modelCode: $modelCode,
                userName: $user,
                ts: $ts,
                tokensInput: (int) ($data['tokens_input'] ?? 0),
                tokensOutput: (int) ($data['tokens_output'] ?? 0),
                tokensCacheRead: (int) ($data['tokens_cache_read'] ?? 0),
                tokensCacheWrite: (int) ($data['tokens_cache_write'] ?? 0),
            );

            $this->repo->saveUsageEvent($event);
            $imported++;
        }

        fclose($handle);

        return $imported;
    }
}
