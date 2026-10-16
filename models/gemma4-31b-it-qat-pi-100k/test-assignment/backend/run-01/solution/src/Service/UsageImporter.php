<?php

namespace App\Service;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class UsageImporter
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function import(string $filePath): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Could not open file: $filePath");
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                $existing = $this->entityManager->getRepository(UsageEvent::class)->findOneBy([
                    'provider' => $data['provider'],
                    'providerRequestId' => $data['provider_request_id']
                ]);

                if ($existing) {
                    continue;
                }

                $event = new UsageEvent();
                $event->setProvider($data['provider']);
                $event->setProviderRequestId($data['provider_request_id']);
                $event->setModel($data['model']);
                $event->setUser($data['user']);
                $event->setTs(new \DateTimeImmutable($data['ts']));
                $event->setTokensInput($data['tokens_input'] ?? 0);
                $event->setTokensOutput($data['tokens_output'] ?? 0);
                $event->setTokensCacheRead($data['tokens_cache_read'] ?? 0);
                $event->setTokensCacheWrite($data['tokens_cache_write'] ?? 0);
                
                $this->entityManager->persist($event);
            }
            $this->entityManager->flush();
        } finally {
            fclose($handle);
        }
    }
}
