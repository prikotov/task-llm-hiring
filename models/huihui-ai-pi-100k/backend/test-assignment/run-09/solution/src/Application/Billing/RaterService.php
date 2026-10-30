<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Rater;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий.
 *
 * Для каждого события берётся строка цен его модели с наибольшим
 * valid_from <= ts. Не найдено — событие остаётся unrated, и каждый
 * следующий запуск повторяет попытку (цены могли подтянуться позже).
 */
final class RaterService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Тарифицирует всё, что ещё ожидает; возвращает сводку запуска.
     *
     * @return array{rated: int, unrated: UsageEvent[]}
     */
    public function run(): array
    {
        return \App\Infrastructure\Transactor::run($this->em, function () {
            /** @var UsageEvent[] $events */
            $events = $this->em->createQuery(
                'SELECT e FROM App\Entity\UsageEvent e WHERE e.status = :status ORDER BY e.id ASC'
            )
                ->setParameter('status', UsageEvent::STATUS_PENDING)
                ->getResult();

            if ($events === []) {
                return ['rated' => 0, 'unrated' => []];
            }

            $providers = $this->indexProviders();
            $models = $this->indexModels($providers);

            $rated = 0;
            $unrated = [];
            $now = new DateTimeImmutable();

            foreach ($events as $event) {
                $provider = $providers[$event->getProviderCode()] ?? null;
                $modelId = $provider !== null ? ($models[$provider->getId() . '|' . $event->getModelCode()] ?? null) : null;
                $priceRow = $modelId !== null ? $this->priceAt($provider->getId(), $modelId, $event->getTs()) : null;

                if ($priceRow === null) {
                    $unrated[] = $event;
                    continue;
                }

                $cost = Rater::cost($event->tokens(), $priceRow->rates(), $provider?->offPeak(), $event->getTs());
                $event->markRated($priceRow->getId(), $cost, $now);
                $rated++;
            }

            return ['rated' => $rated, 'unrated' => $unrated];
        });
    }

    /**
     * Строка цен модели с наибольшим valid_from <= $ts (valid_from включительно).
     */
    private function priceAt(int $providerId, int $modelId, DateTimeImmutable $ts): ?PriceRow
    {
        $rows = $this->em->createQuery(
            'SELECT r FROM App\Entity\PriceRow r WHERE r.providerId = :p AND r.modelId = :m AND r.validFrom <= :ts ORDER BY r.validFrom DESC'
        )
            ->setParameter('p', $providerId)
            ->setParameter('m', $modelId)
            ->setParameter('ts', $ts)
            ->setMaxResults(1)
            ->getResult();

        return $rows[0] ?? null;
    }

    /** @return array<string, Provider> code => provider */
    private function indexProviders(): array
    {
        $index = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $provider) {
            $index[$provider->getCode()] = $provider;
        }

        return $index;
    }

    /** @return array<string, int> "providerId|modelCode" => modelId */
    private function indexModels(array $providers): array
    {
        $index = [];
        if ($providers === []) {
            return $index;
        }
        $modelClass = \App\Entity\Model::class;
        foreach ($this->em->getRepository($modelClass)->findAll() as $model) {
            $index[$model->getProviderId() . '|' . $model->getCode()] = $model->getId();
        }

        return $index;
    }
}
