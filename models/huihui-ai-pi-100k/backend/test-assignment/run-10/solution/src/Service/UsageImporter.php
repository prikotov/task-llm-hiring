<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\UsageEventData;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий с дедупликацией по ключу (provider, provider_request_id).
 *
 * Повторная доставка уже известного события игнорируется целиком
 * (без обновлений и доначислений) — правило 5 из Taks.md.
 */
final class UsageImporter
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param UsageEventData[] $events
     * @return array{inserted:int, skipped:int}
     */
    public function import(array $events): array
    {
        $result = ['inserted' => 0, 'skipped' => 0];
        $createdAt = new \DateTimeImmutable();

        $this->em->wrapInTransaction(function () use ($events, $createdAt, &$result): void {
            $repo = $this->em->getRepository(UsageEvent::class);
            $seen = [];

            foreach ($events as $data) {
                $key = $data->provider . "\x00" . $data->providerRequestId;

                if (isset($seen[$key])
                    || $repo->existsByKey($data->provider, $data->providerRequestId)) {
                    $seen[$key] = true;
                    $result['skipped']++;
                    continue;
                }

                $seen[$key] = true;
                $this->em->persist(
                    new UsageEvent(
                        $data->provider,
                        $data->providerRequestId,
                        $data->model,
                        $data->user,
                        $data->ts,
                        $data->counts,
                        $createdAt,
                    )
                );
                $result['inserted']++;
            }
        });

        return $result;
    }
}
