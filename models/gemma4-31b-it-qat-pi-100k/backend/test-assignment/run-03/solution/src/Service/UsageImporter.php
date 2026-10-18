<?php

namespace App\Service;

use App\Entity\Model;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class UsageImporter
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function import(string $filePath): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \InvalidArgumentException("Could not open file: $filePath");
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') continue;

            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                fclose($handle);
                throw new \InvalidArgumentException('Invalid JSONL line: ' . json_last_error_msg());
            }

            // Deduplication by (provider, provider_request_id)
            $existing = $this->entityManager->getRepository(UsageEvent::class)->findOneBy([
                'providerCode' => $data['provider'],
                'providerRequestId' => $data['provider_request_id']
            ]);

            if ($existing) {
                continue;
            }

            $event = new UsageEvent();
            $event->providerCode = $data['provider'];
            $event->providerRequestId = $data['provider_request_id'];
            $event->modelCode = $data['model'];
            $event->userName = $data['user'];
            $event->ts = new DateTimeImmutable($data['ts']);
            $event->tokensInput = $data['tokens_input'] ?? 0;
            $event->tokensOutput = $data['tokens_output'] ?? 0;
            $event->tokensCacheRead = $data['tokens_cache_read'] ?? 0;
            $event->tokensCacheWrite = $data['tokens_cache_write'] ?? 0;

            $this->entityManager->persist($event);
        }

        fclose($handle);
        $this->entityManager->flush();
    }
}
