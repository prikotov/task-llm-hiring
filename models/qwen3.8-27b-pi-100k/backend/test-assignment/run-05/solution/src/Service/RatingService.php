<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\BillingCalculator;
use App\Domain\Money;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Entity\UsageEventStatus;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий.
 *
 * Возвращает [rated, unrated] — количества событий, рассчитанных и оставшихся
 * unrated в этом запуске; unrated-события передаются в $onUnrated.
 */
final class RatingService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param callable(string, string, \DateTimeImmutable): void|null $onUnrated
     *
     * @return array{0: int, 1: int}
     */
    public function rateAll(?callable $onUnrated = null): array
    {
        $modelByReference = $this->loadModels();
        $rowsByModel = $this->loadPriceRows($modelByReference);
        $providerByCode = $this->loadProviders();

        $repository = $this->em->getRepository(UsageEvent::class);
        $unrated = array_merge(
            $repository->findBy(['status' => UsageEventStatus::NEW]),
            $repository->findBy(['status' => UsageEventStatus::UNRATED]),
        );

        $rated = 0;
        $stillUnrated = 0;

        $this->em->wrapInTransaction(function () use ($unrated, $modelByReference, $rowsByModel, $providerByCode, $onUnrated, &$rated, &$stillUnrated): void {
            foreach ($unrated as $event) {
                $row = $this->selectPriceRow($event, $modelByReference, $rowsByModel);
                if ($row === null) {
                    $event->markUnrated();
                    $stillUnrated++;
                    if ($onUnrated !== null) {
                        $onUnrated($event->getProvider(), $event->getProviderRequestId(), $event->getTs());
                    }
                    continue;
                }

                $provider = $providerByCode[$event->getProvider()] ?? null;
                $exact = BillingCalculator::computeExact(
                    $row,
                    $provider,
                    $event->getTs(),
                    $event->getTokensInput(),
                    $event->getTokensOutput(),
                    $event->getTokensCacheRead(),
                    $event->getTokensCacheWrite(),
                );
                if ($exact === null) {
                    $event->markUnrated();
                    $stillUnrated++;
                    if ($onUnrated !== null) {
                        $onUnrated($event->getProvider(), $event->getProviderRequestId(), $event->getTs());
                    }
                    continue;
                }

                // Единственное округление: HALF_UP до 6 знаков по итогу события.
                $event->markRated(Money::round6($exact), $row);
                $rated++;
            }
            $this->em->flush();
        });

        return [$rated, $stillUnrated];
    }

    private function selectPriceRow(
        UsageEvent $event,
        array $modelByReference,
        array $rowsByModel,
    ): ?PriceRow {
        $model = $modelByReference[$event->getProvider().'/'.$event->getModel()] ?? null;
        if ($model === null) {
            return null;
        }

        // Строка с наибольшим valid_from <= ts (valid_from включительно).
        $rows = $rowsByModel[$model->getId()] ?? [];
        foreach ($rows as $row) {
            if ($row->getValidFrom() <= $event->getTs()) {
                return $row;
            }
        }

        return null;
    }

    /** @return array<string, Model> ключ "providerCode/modelCode" */
    private function loadModels(): array
    {
        $map = [];
        foreach ($this->em->getRepository(Model::class)->findAll() as $model) {
            $map[$model->getProvider()->getCode().'/'.$model->getCode()] = $model;
        }

        return $map;
    }

    /**
     * @param array<string, Model> $modelByReference
     *
     * @return array<int, list<PriceRow>> по id модели, отсортировано по validFrom убыванию
     */
    private function loadPriceRows(array $modelByReference): array
    {
        $rowsByModel = [];
        foreach ($modelByReference as $model) {
            /** @var list<PriceRow> $rows */
            $rows = $this->em->getRepository(PriceRow::class)->findBy(['model' => $model->getId()], ['validFrom' => 'DESC']);
            $rowsByModel[$model->getId()] = $rows;
        }

        return $rowsByModel;
    }

    /** @return array<string, Provider> */
    private function loadProviders(): array
    {
        $map = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $provider) {
            $map[$provider->getCode()] = $provider;
        }

        return $map;
    }
}
