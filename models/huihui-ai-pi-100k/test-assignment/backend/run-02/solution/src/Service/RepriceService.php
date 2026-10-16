<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Domain\RateCalculator;
use App\Entity\Price;
use App\Entity\RepriceLog;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use App\Import\PriceRowDefinition;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Файл коррекции содержит строки цен (provider/model должны существовать в
 * справочнике БД; секции providers/models допустимо опустить).
 * Механизм: затронутые строки цен обновляются (upsert по
 * (provider, model, valid_from)), рассчитанные по ним события пересчитываются
 * на месте (стоимость перезаписывается — дублирования в отчёте нет), каждая
 * корректировка фиксируется в журнале reprice_log (старая/новая стоимость).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
        private readonly ProviderRepository $providers,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @param list<PriceRowDefinition> $rows
     *
     * @return array{events: int, delta: Money} delta = новая сумма − старая
     *
     * @throws ImportException если провайдер/модель не найдены в справочнике
     */
    public function apply(array $rows): array
    {
        return $this->em->wrapInTransaction(function () use ($rows): array {
            /** @var list<Price> $touched */
            $touched = [];
            foreach ($rows as $definition) {
                $touched[] = $this->upsertPrice($definition);
            }

            $this->em->flush(); // после flush у строк цен есть идентификаторы

            $priceIds = array_values(array_unique(array_map(
                static fn (Price $price): int => (int) $price->id,
                $touched,
            )));

            $delta = Money::zero();
            $count = 0;
            foreach ($this->events->findRatedByPriceIds($priceIds) as $event) {
                $oldCost = $event->costMoney();
                $newCost = $this->recalculateCost($event);

                $event->markRated($newCost, $event->price);
                $this->em->persist(new RepriceLog($event, $event->price, $oldCost, $newCost));

                $delta = $delta->plus($newCost)->minus($oldCost);
                $count++;
            }

            $this->em->flush();

            return ['events' => $count, 'delta' => $delta];
        });
    }

    /**
     * Upsert строки цен по естественному ключу справочника.
     *
     * @throws ImportException
     */
    private function upsertPrice(PriceRowDefinition $definition): Price
    {
        $model = $this->models->findByProviderCodeAndCode($definition->providerCode, $definition->modelCode);
        if ($model === null) {
            throw new ImportException(sprintf(
                'Ретро-коррекция %s/%s (valid_from %s): модель не найдена в справочнике',
                $definition->providerCode,
                $definition->modelCode,
                \gmdate('c', $definition->validFrom->getTimestamp()),
            ));
        }

        $price = $this->prices->findByModelAndValidFrom($model, $definition->validFrom);
        if ($price === null) {
            $price = new Price();
            $this->em->persist($price);
        }

        $price->model = $model;
        $price->validFrom = $definition->validFrom;
        $price->currency = $definition->currency;
        $price->applyRates($definition->unit->value, $definition->rates);

        return $price;
    }

    /** Пересчёт стоимости события по текущим значениям его строки цен и оф-пика провайдера. */
    private function recalculateCost(UsageEvent $event): Money
    {
        if ($event->price === null) {
            throw new \LogicException('Ретро-коррекция: у оценённого события не задана строка цен');
        }

        $provider = $this->providers->findByCode($event->provider);
        $offPeak = $provider?->offPeak();

        return RateCalculator::eventCost(
            $event->tokenUsage(),
            $event->price->pricingUnit(),
            $event->price->ratesByKind(),
            $offPeak,
            $event->ts,
        );
    }
}
