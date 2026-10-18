<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class UsageImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function import(string $filePath): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException('Cannot open file: ' . $filePath);
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $data = json_decode($line, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Invalid JSON line: ' . json_last_error_msg());
                }

                $provider = $this->entityManager->getRepository(Provider::class)->find($data['provider']);
                if (!$provider) {
                    throw new \RuntimeException('Provider not found: ' . $data['provider']);
                }

                // Check for duplicates: (provider, provider_request_id)
                $duplicate = $this->entityManager->getRepository(UsageEvent::class)->findOneBy([
                    'provider' => $provider,
                    'providerRequestId' => $data['provider_request_id'],
                ]);

                if ($duplicate) {
                    continue;
                }

                $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                    'provider' => $provider,
                    'code' => $data['model'],
                ]);
                if (!$model) {
                    throw new \RuntimeException('Model not found: ' . $data['provider'] . '/' . $data['model']);
                }

                $event = new UsageEvent(
                    $provider,
                    $data['provider_request_id'],
                    $model,
                    $data['user'],
                    new \DateTimeImmutable($data['ts']),
                    (int)$data['tokens_input'],
                    (int)$data['tokens_output'],
                    (int)$data['tokens_cache_read'],
                    (int)$data['tokens_cache_write']
                );

                $this->entityManager->persist($event);
            }
        } finally {
            fclose($handle);
        }

        $this->entityManager->flush();
    }
}
