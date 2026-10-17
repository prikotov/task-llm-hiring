<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UsageEvent;
use App\Import\UsageEventInput;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём журнала событий использования с дедупликацией по
 * (provider, provider_request_id) — повторная доставка игнорируется,
 * никаких обновлений и доначислений.
 */
final class UsageImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @param list<UsageEventInput> $inputs
     *
     * @return array{imported: int, duplicates: int}
     */
    public function import(array $inputs): array
    {
        return $this->em->wrapInTransaction(function () use ($inputs): array {
            $imported = 0;
            $duplicates = 0;

            foreach ($inputs as $input) {
                if ($this->events->existsByProviderAndRequestId($input->provider, $input->providerRequestId)) {
                    $duplicates++;
                    continue;
                }

                $event = new UsageEvent();
                $event->provider = $input->provider;
                $event->providerRequestId = $input->providerRequestId;
                $event->model = $input->model;
                $event->user = $input->user;
                $event->ts = $input->ts;
                $event->tokensInput = $input->tokens->input;
                $event->tokensOutput = $input->tokens->output;
                $event->tokensCacheRead = $input->tokens->cacheRead;
                $event->tokensCacheWrite = $input->tokens->cacheWrite;

                $this->em->persist($event);
                $imported++;
            }

            $this->em->flush();

            return ['imported' => $imported, 'duplicates' => $duplicates];
        });
    }
}
