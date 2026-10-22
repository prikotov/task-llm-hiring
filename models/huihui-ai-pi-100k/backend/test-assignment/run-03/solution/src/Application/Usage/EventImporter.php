<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\Common\Iso8601Utc as Utc;
use App\Persistence\Entity\UsageEvent;
use App\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования с дедупликацией (п. 5 TASK.md):
 * ключ — (provider, provider_request_id). Повторная доставка уже известного
 * события целиком игнорируется: без обновлений и доначислений.
 *
 * Событие может ссылаться на ещё не загруженные в справочнике провайдера
 * или модель — такое событие сохраняется и останется unrated,
 * пока цены не подтянутся (повторные попытки в app:billing:rate).
 */
final class EventImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @param list<UsageEventSpec> $specs
     * @return array{imported: int, duplicates: int}
     */
    public function import(array $specs): array
    {
        if ($specs === []) {
            return ['imported' => 0, 'duplicates' => 0];
        }

        return $this->em->wrapInTransaction(function () use ($specs): array {
            $providers = [];
            $requestIds = [];
            foreach ($specs as $spec) {
                $providers[$spec->provider] = true;
                $requestIds[$spec->providerRequestId] = true;
            }

            $known = [];
            foreach ($this->events->findExistingKeys(array_keys($providers), array_keys($requestIds)) as $row) {
                $known[$row['provider']."\0".$row['providerRequestId']] = true;
            }

            $imported = 0;
            $duplicates = 0;
            foreach ($specs as $spec) {
                $key = $spec->provider."\0".$spec->providerRequestId;
                if (isset($known[$key])) {
                    $duplicates++;
                    continue;
                }
                $known[$key] = true;

                $entity = new UsageEvent();
                $entity->provider = $spec->provider;
                $entity->model = $spec->model;
                $entity->providerRequestId = $spec->providerRequestId;
                $entity->user = $spec->user;
                $entity->ts = Utc::toMutableUtc($spec->ts);
                $entity->tokensInput = $spec->tokensInput;
                $entity->tokensOutput = $spec->tokensOutput;
                $entity->tokensCacheRead = $spec->tokensCacheRead;
                $entity->tokensCacheWrite = $spec->tokensCacheWrite;

                $this->em->persist($entity);
                $imported++;
            }

            $this->em->flush();

            return ['imported' => $imported, 'duplicates' => $duplicates];
        });
    }
}
