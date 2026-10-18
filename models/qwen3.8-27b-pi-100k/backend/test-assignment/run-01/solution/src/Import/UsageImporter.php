<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * Приём событий использования с дедупликацией по (provider, provider_request_id):
 * повторная доставка уже известного события целиком игнорируется — без
 * обновлений и доначислений. Первая поставка события становится канонической.
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $usageEventRepository,
    ) {
    }

    /**
     * @param list<UsageEventLine> $lines
     *
     * @return array{inserted:int, duplicates:int}
     */
    public function import(array $lines): array
    {
        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            $inserted = 0;
            $duplicates = 0;
            $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

            foreach ($lines as $line) {
                $exists = $this->usageEventRepository->findByProviderAndRequestId($line->provider, $line->requestId) !== null;
                if ($exists) {
                    ++$duplicates;

                    continue;
                }

                $this->em->persist(new UsageEvent(
                    $line->provider,
                    $line->requestId,
                    $line->model,
                    $line->user,
                    $line->ts,
                    $line->tokensInput,
                    $line->tokensOutput,
                    $line->tokensCacheRead,
                    $line->tokensCacheWrite,
                    $now,
                ));
                ++$inserted;
            }

            $this->em->flush();
            $conn->commit();

            return ['inserted' => $inserted, 'duplicates' => $duplicates];
        } catch (Throwable $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }

            if ($e instanceof ImportException) {
                throw $e;
            }

            throw new ImportException('Не удалось загрузить события использования: ' . $e->getMessage(), 0, $e);
        }
    }
}
