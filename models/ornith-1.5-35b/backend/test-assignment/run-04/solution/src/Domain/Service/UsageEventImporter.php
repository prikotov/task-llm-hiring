<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка событий использования по правилу п. 5（дедупликация по
 * (provider, provider_request_id); повторная доставка игнорируется полностью）。
 */
final class UsageEventImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param list<array> $rows распаршенные строки JSONL
     *
     * @throws \InvalidArgumentException при некорректной строке
     *
     * @return int количество вновь добавленных событий
     */
    public function import(array $rows): int
    {
        /** @var UsageEvent[] $repo */
        $repo = $this->em->getRepository(UsageEvent::class);

        $em = $this->em;
        $em->beginTransaction();
        try {
            $added = 0;
            foreach ($rows as $row) {
                $existing = $repo->findBy(['providerCode' => $row['provider'], 'providerRequestId' => $row['provider_request_id']]);
                if ($existing) {
                    continue; // дубль — игнорируем
                }
                $repo->persist(new UsageEvent(
                    (string) $row['provider'],
                    (string) $row['provider_request_id'],
                    (string) $row['model'],
                    (string) $row['user'],
                    new \DateTimeImmutable($row['ts']),
                    (int) $row['tokens_input'],
                    (int) $row['tokens_output'],
                    (int) $row['tokens_cache_read'],
                    (int) $row['tokens_cache_write'],
                ));
                ++$added;
            }
            $em->flush();
            $em->commit();

            return $added;
        } catch (\Throwable $e) {
            $em->rollBack();
            throw $e;
        }
    }
}
