<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Entity\UsageEvent;
use App\Billing\Import\Dto\UsageEventInput;
use App\Billing\Repository\UsageEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования с идемпотентностью (п. 5):
 * ключ дедупликации — (provider, provider_request_id);
 * повторная доставка уже известного события игнорируется целиком
 * (никаких обновлений, никаких доначислений).
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @param iterable<UsageEventInput> $inputs
     */
    public function import(iterable $inputs): UsageImportResult
    {
        return $this->em->wrapInTransaction(function () use ($inputs): UsageImportResult {
            $accepted = 0;
            $duplicates = 0;

            foreach ($inputs as $input) {
                $existing = $this->em->find(UsageEvent::class, [
                    'provider' => $input->provider,
                    'providerRequestId' => $input->providerRequestId,
                ]);
                if ($existing !== null) {
                    $duplicates++;
                    continue;
                }

                $this->em->persist(new UsageEvent(
                    $input->provider,
                    $input->providerRequestId,
                    $input->model,
                    $input->user,
                    $input->ts,
                    $input->tokens,
                    new DateTimeImmutable(),
                ));
                $accepted++;
            }

            $this->em->flush();

            return new UsageImportResult($accepted, $duplicates);
        });
    }
}
