<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\Pricing\Model;
use App\Domain\Pricing\Provider;
use App\Domain\Usage\UsageEvent;
use App\Exception\InvalidUsageFileException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий (п. 5 задания): дедупликация по (provider, provider_request_id),
 * повторная доставка игнорируется целиком — никаких обновлений и доначислений.
 *
 * Валидное событие может ссыласться только на провайдера и модель, существующие
 * в загруженном справочнике (иначе импорт — ошибка, код 1).
 */
final class UsageImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param UsageEventSpec[] $specs
     *
     * @return array{inserted:int, duplicated:int}
     *
     * @throws InvalidUsageFileException
     */
    public function import(array $specs): array
    {
        $em = $this->em;

        return $em->getConnection()->transactional(static function () use ($em, $specs): array {
            $inserted = 0;
            $duplicated = 0;
            /** @var array<string, true> $seenInFile */
            $seenInFile = [];

            foreach ($specs as $spec) {
                $dedupeKey = $spec->provider."\x1f".$spec->providerRequestId;
                if (isset($seenInFile[$dedupeKey])) {
                    $duplicated++;
                    continue;
                }
                $seenInFile[$dedupeKey] = true;

                $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $spec->provider])
                    ?? throw new InvalidUsageFileException(sprintf('line %d: unknown provider "%s"', $spec->line, $spec->provider));
                $model = $em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $spec->model])
                    ?? throw new InvalidUsageFileException(sprintf('line %d: unknown model "%s/%s"', $spec->line, $spec->provider, $spec->model));

                $exists = $em->createQuery(
                    'SELECT e.id FROM App\\Domain\\Usage\\UsageEvent e WHERE e.provider = :provider AND e.providerRequestId = :requestId'
                )
                    ->setParameter('provider', $provider)
                    ->setParameter('requestId', $spec->providerRequestId)
                    ->getOneOrNullResult();

                if ($exists !== null) {
                    $duplicated++;
                    continue;
                }

                $em->persist(new UsageEvent(
                    $provider,
                    $model,
                    $spec->providerRequestId,
                    $spec->user,
                    $spec->ts,
                    $spec->tokens,
                ));
                $inserted++;
            }

            $em->flush();

            return ['inserted' => $inserted, 'duplicated' => $duplicated];
        });
    }
}
