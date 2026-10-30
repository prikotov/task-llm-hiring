<?php

declare(strict_types=1);

namespace App\Application\Rating;

use App\Domain\Billing\CostCalculator;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PriceRow as DomainPriceRow;
use App\Infrastructure\Doctrine\Entity\ModelEntity;
use App\Infrastructure\Doctrine\Entity\PriceRowEntity;
use App\Infrastructure\Doctrine\Entity\ProviderEntity;
use App\Infrastructure\Doctrine\Entity\UsageEventEntity;
use App\Infrastructure\Doctrine\Repository\ModelRepository;
use App\Infrastructure\Doctrine\Repository\PriceRowRepository;
use App\Infrastructure\Doctrine\Repository\ProviderRepository;
use App\Infrastructure\Doctrine\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация нерассчитанных событий (cost_usd IS NULL).
 *
 * Событие без подходящей цены остаётся unrated и будет повторно
 * обработано при следующем запуске — цены могли появиться позже.
 */
final class RateUsageEvents
{
    private const BATCH_SIZE = 500;

    /** @var array<string, ProviderEntity|null> */
    private array $providerCache = [];

    /** @var array<string, ModelEntity|null> */
    private array $modelCache = [];

    /** @var array<int, list<PriceRowEntity>> */
    private array $priceCache = [];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRowRepository $priceRows,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * @return array{rated: int, unrated: int, unratedEvents: list<UsageEventEntity>}
     */
    public function __invoke(): array
    {
        $rated = 0;
        $unrated = 0;
        $unratedEvents = [];
        $lastId = 0;

        do {
            /** @var list<UsageEventEntity> $batch */
            $batch = $this->events->findUnrated(self::BATCH_SIZE, $lastId);

            foreach ($batch as $event) {
                $lastId = (int) $event->id();
                $result = $this->rateEvent($event);

                if (null === $result) {
                    ++$unrated;
                    $unratedEvents[] = $event;
                    continue;
                }

                [$priceRowId, $cost] = $result;
                $event->applyPrice($priceRowId, $cost);
                ++$rated;
            }

            $this->em->flush();
            $this->em->clear();

            // Кэш привязан к сущностям, которые после clear() уже detached.
            $this->providerCache = [];
            $this->modelCache = [];
            $this->priceCache = [];
            gc_collect_cycles();
        } while (count($batch) === self::BATCH_SIZE);

        return ['rated' => $rated, 'unrated' => $unrated, 'unratedEvents' => $unratedEvents];
    }

    /**
     * @return array{0: int, 1: string}|null id строки цен и стоимость, либо null для unrated
     */
    private function rateEvent(UsageEventEntity $event): ?array
    {
        $provider = $this->provider($event->providerCode());
        $model = $this->model($provider, $event->modelCode());

        if (null === $provider || null === $model) {
            return null;
        }

        $rows = $this->priceCache[$model->id() ?? 0] ??= $this->priceRows->findAllForModel($model);
        $priceRow = $this->pickPriceRow($rows, $event);
        if (null === $priceRow) {
            return null;
        }

        $domainProvider = $provider->toDomain();
        $domainModel = $model->toDomain();
        $cost = $this->calculator->costOfRow(
            $domainProvider,
            $domainModel,
            $priceRow->toDomain($domainProvider),
            $event->occurredAt(),
            $event->tokens(),
        );

        return [$priceRow->id() ?? 0, $cost->fixed(CostCalculator::COST_SCALE)];
    }

    /**
     * Наибольший valid_from <= ts события (valid_from включительно).
     *
     * @param list<PriceRowEntity> $rows
     */
    private function pickPriceRow(array $rows, UsageEventEntity $event): ?PriceRowEntity
    {
        $best = null;
        foreach ($rows as $row) {
            if ($row->validFrom() > $event->occurredAt()) {
                continue;
            }
            if (null === $best || $row->validFrom() > $best->validFrom()) {
                $best = $row;
            }
        }

        return $best;
    }

    private function provider(string $code): ?ProviderEntity
    {
        return array_key_exists($code, $this->providerCache)
            ? $this->providerCache[$code]
            : ($this->providerCache[$code] = $this->providers->findByCode($code));
    }

    private function model(?ProviderEntity $provider, string $code): ?ModelEntity
    {
        if (null === $provider) {
            return null;
        }

        $key = $provider->code() . '/' . $code;

        return array_key_exists($key, $this->modelCache)
            ? $this->modelCache[$key]
            : ($this->modelCache[$key] = $this->models->findOne($provider, $code));
    }
}
