<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UsageImportService
{
    public function __construct(
        private readonly UsageEventRepository $events,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{imported:int,duplicates:int}
     */
    public function importJsonl(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new \RuntimeException("cannot read file: {$path}");
        }
        $imported = 0;
        $duplicates = 0;
        foreach ($lines as $line) {
            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException("invalid JSONL: {$e->getMessage()}");
            }
            if (!is_array($data)) {
                throw new \RuntimeException('invalid JSONL line (not an object)');
            }
            $ev = $this->buildEvent($data);
            $existing = $this->events->findByNaturalKey($ev->providerCode(), $ev->providerRequestId());
            if ($existing !== null) {
                $duplicates++;
                continue;
            }
            $this->em->persist($ev);
            $imported++;
        }
        $this->em->flush();
        return ['imported' => $imported, 'duplicates' => $duplicates];
    }

    /** @param array<string,mixed> $data */
    private function buildEvent(array $data): UsageEvent
    {
        $provider = (string) ($data['provider'] ?? '');
        $id = (string) ($data['provider_request_id'] ?? '');
        $model = (string) ($data['model'] ?? '');
        $user = (string) ($data['user'] ?? '');
        try {
            $ts = new \DateTimeImmutable((string) ($data['ts'] ?? ''), new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new \RuntimeException('invalid or missing "ts" (ISO-8601 UTC)');
        }
        $ti = (int) ($data['tokens_input'] ?? 0);
        $to = (int) ($data['tokens_output'] ?? 0);
        $tr = (int) ($data['tokens_cache_read'] ?? 0);
        $tw = (int) ($data['tokens_cache_write'] ?? 0);
        foreach ([$ti, $to, $tr, $tw] as $v) {
            if ($v < 0) {
                throw new \RuntimeException('token counts must be >= 0');
            }
        }
        if ($provider === '' || $id === '' || $model === '' || $user === '') {
            throw new \RuntimeException('missing required field(s)');
        }
        return new UsageEvent($provider, $id, $model, $user, $ts, $ti, $to, $tr, $tw);
    }
}
