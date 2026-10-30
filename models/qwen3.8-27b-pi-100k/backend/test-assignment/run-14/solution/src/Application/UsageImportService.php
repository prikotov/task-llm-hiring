<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Inbound\UsageInputParser;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\InvalidInputException;

final class UsageImportService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UsageEventRepository $events,
    ) {
    }

    /**
     * @throws InvalidInputException
     * @throws \RuntimeException
     */
    public function importFile(string $path): bool
    {
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('File not found: %s', $path));
        }
        $jsonl = @file_get_contents($path);
        if ($jsonl === false) {
            throw new \RuntimeException(sprintf('Cannot read: %s', $path));
        }

        return $this->import($jsonl);
    }

    /**
     * Возвращает true если добавлены новые события, false если все — дубли.
     *
     * @throws InvalidInputException
     */
    public function import(string $jsonl): bool
    {
        $rows = UsageInputParser::parse($jsonl);
        $inserted = false;

        foreach ($rows as $row) {
            $existing = $this->events->findByProviderAndRequestId(
                $row['provider'],
                $row['provider_request_id'],
            );

            if ($existing !== null) {
                // Дедупликация (rule 5): повторная доставка игнорируется целиком.
                continue;
            }

            $t = $row['tokens'];
            $event = UsageEvent::create(
                $row['provider'],
                $row['provider_request_id'],
                $row['model'],
                $row['user'],
                $row['ts'],
                $t['tokens_input'],
                $t['tokens_output'],
                $t['tokens_cache_read'],
                $t['tokens_cache_write'],
            );
            $this->em->persist($event);
            $inserted = true;
        }

        $this->em->flush();
        $this->em->clear();
        return $inserted;
    }
}
