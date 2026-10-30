<?php

declare(strict_types=1);

namespace App\Billing\Application\Reprice;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Application\Import\PriceCatalogData;
use App\Billing\Domain\Catalog\CostAdjustment;
use App\Billing\Domain\Catalog\Model;
use App\Billing\Domain\Catalog\Price;
use App\Billing\Domain\Shared\Decimal;
use App\Billing\Domain\Shared\Time;
use App\Billing\Infrastructure\Persistence\ModelRepository;
use App\Billing\Infrastructure\Persistence\PriceRepository;
use App\Billing\Infrastructure\Persistence\UsageEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цен.
 *
 * 1. Исправленные строки применяются upsert'ом по (provider, model, valid_from);
 *    провайдер и модель должны существовать в справочнике.
 * 2. Пересчитываются уже рассчитанные события, тарифицированные ИСПРАВЛЕННЫМИ строками.
 *    Для каждого изменившегося события пишется CostAdjustment (старое/новое значение).
 *    Само событие не удаляется и не дублируется, поэтому отчёт не задваивает суммы.
 *
 * Строки цен, которые тарифицировали события, меняются in place (id сохраняется),
 * поэтому связь событие→строка цен остаётся валидной.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
        private readonly UsageEventRepository $events,
    ) {
    }

    public function reprice(PriceCatalogData $correction): RepriceResult
    {
        return $this->em->wrapInTransaction(function () use ($correction): RepriceResult {
            /** @var array<int, true> $correctedPriceIds id исправленных строк цен */
            $correctedPriceIds = [];
            /** @var array<string, array{model: Model, from: DateTimeImmutable}> $ranges по "provider/model" */
            $ranges = [];

            foreach ($correction->prices as $p) {
                $model = $this->models->findOneByKey($p['provider'], $p['model'])
                    ?? throw new InvalidInputException(sprintf('Неизвестная модель: %s/%s', $p['provider'], $p['model']));

                $price = $this->prices->findOneByKey($model, $p['validFrom']);
                if ($price === null) {
                    $price = new Price($model, $p['validFrom'], $p['currency'], $p['unit'], $p['rates']);
                    $this->em->persist($price);
                    $this->em->flush();
                } else {
                    $price->changeRates($p['unit'], $p['rates']);
                }
                $correctedPriceIds[$price->id()] = true;

                $key = $p['provider'].'/'.$p['model'];
                if (!isset($ranges[$key]) || $p['validFrom'] < $ranges[$key]['from']) {
                    $ranges[$key] = ['model' => $model, 'from' => $p['validFrom']];
                }
            }
            $this->em->flush();

            $now = new DateTimeImmutable('now', Time::utc());
            $repriced = 0;
            $delta = '0';

            foreach ($ranges as $range) {
                // Берём все рассчитанные события с ts >= from: исправление может добавить строку,
                // на которую теперь попадают события, тарифицированные ранее другой строкой.
                foreach ($this->events->findRatedForModelFrom(
                    $range['model']->provider()->code(),
                    $range['model']->code(),
                    $range['from'],
                ) as $event) {
                    $oldPrice = $event->price();
                    $oldCost = (string) $event->costUsd();

                    $newPrice = $this->prices->findActiveFor($range['model'], $event->ts());
                    if ($newPrice === null) {
                        continue; // не бывает: событие уже тарифицировано этой моделью
                    }

                    $touchesCorrection = isset($correctedPriceIds[$newPrice->id()])
                        || ($oldPrice !== null && isset($correctedPriceIds[$oldPrice->id()]));
                    if (!$touchesCorrection) {
                        continue;
                    }

                    $event->rateWith($newPrice);
                    $newCost = (string) $event->costUsd();
                    ++$repriced;

                    $change = Decimal::add($newCost, Decimal::mul($oldCost, '-1', Decimal::MONEY_SCALE), Decimal::MONEY_SCALE);
                    if (Decimal::compare($change, '0', Decimal::MONEY_SCALE) === 0) {
                        continue;
                    }

                    $this->em->persist(new CostAdjustment($event, $oldCost, $newCost, 'price_correction', $now));
                    $delta = Decimal::add($delta, $change, Decimal::MONEY_SCALE);
                }
            }
            $this->em->flush();

            return new RepriceResult($repriced, Decimal::formatMoney($delta));
        });
    }
}
