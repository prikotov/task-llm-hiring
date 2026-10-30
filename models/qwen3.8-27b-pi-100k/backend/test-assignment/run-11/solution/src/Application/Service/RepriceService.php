<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Exception\BillingException;
use App\Domain\Money;
use App\Domain\OffPeakWindow;
use App\Domain\PriceSheet;
use App\Domain\RateCalculator;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Entity\UsageRating;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция: upsert цены из файла, пересчёт событий записанных
 * по этой строке, вывод (n, delta).
 *
 * Механизм:
 *  - upsert по (provider, model, valid_from) в prices;
 *  - найти все usage_ratings с price_id этой строки (после replace,
 *    их price_id уже соответствует новой строке id — поэтому ищем по
 *    id, который сохраняется как «исторический» price_id при rate).
 *
 * Мы фиксируем старый amount (из usage_ratings.amount) и пересчитываем
 * event на новой строке; записываем новый usage_ratings-журнал с
 * source='reprice', delta = sum(new) - sum(old).
 *
 * Текущая стоимость события = последнее (id) значение в usage_ratings.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceBookService $priceBook,
        private readonly RateCalculator $calculator = new RateCalculator(),
    ) {
    }

    /**
     * @param array{prices?:array} $data
     * @return array{n:int,deltaUsd:Money}
     */
    public function reprice(array $data): array
    {
        $prices = (array)($data['prices'] ?? []);
        if ($prices === []) {
            throw BillingException::parseError('в файле коррекции нет секции prices');
        }

        // для каждой строки: upsert в prices; соберём map newId -> price.
        $newPriceIds = [];
        foreach ($prices as $p) {
            foreach (['provider', 'model', 'valid_from', 'unit', 'rates'] as $key) {
                if (!isset($p[$key])) {
                    throw BillingException::parseError(sprintf('prices[]: отсутствует поле "%s"', $key));
                }
            }
            $providerCode = (string) $p['provider'];
            $modelCode = (string) $p['model'];
            $validFrom = PriceBookService::parseIsoUtzPublic((string) $p['valid_from']);
            $currency = (string) ($p['currency'] ?? 'USD');
            $unit = (string) $p['unit'];
            $rates = PriceBookService::parseRatesPublic($p['rates'], $unit);

            $model = $this->priceBook->modelFor($providerCode, $modelCode);
            if ($model === null) {
                throw BillingException::notFound('модель', $providerCode . '/' . $modelCode);
            }
            $existing = $this->em->getRepository(Price::class)->findOneBy([
                'model' => $model,
                'validFrom' => $validFrom,
            ]);
            if ($existing === null) {
                $new = new Price($model, $validFrom, $currency, $unit, $rates);
                $this->em->persist($new);
                $this->em->flush();
                $newId = $new->id();
                $newPriceIds[$newId] = $new;
            } else {
                $existing->replace($currency, $unit, $rates);
                $newId = $existing->id();
                $newPriceIds[$newId] = $existing;
            }
        }
        $this->em->flush();

        // 2. Найти все события, чей «исторический price_id» входит в
        //    set обновлённых строк. Итоговая стоимость события
        //    = last(id) в usage_ratings по event.
        $idList = array_map('intval', array_keys($newPriceIds));
        if ($idList === []) {
            return ['n' => 0, 'deltaUsd' => Money::zero()];
        }
        $ratingRows = $this->em->getConnection()->fetchAllAssociative(
            'SELECT r.id, r.event_id, r.amount, e.id AS ev_id, e.provider, e.model, e.ts
             FROM usage_ratings r
             JOIN usage_events e ON e.id = r.event_id
             WHERE r.price_id IN (' . implode(',', $idList) . ')'
        );
        // собираем события с их старым amount (последний rating по price_id)
        // и пересчитываем по новой строке
        $eventsTouched = [];
        foreach ($ratingRows as $row) {
            $event = $this->em->getRepository(UsageEvent::class)->find((int) $row['ev_id']);
            if ($event === null) {
                continue;
            }
            if (!isset($eventsTouched[(int) $event->id()])) {
                $eventsTouched[(int) $event->id()] = [
                    'event' => $event,
                    'oldAmount' => Money::of($row['amount']),
                ];
            }
        }

        $n = 0;
        $delta = Money::zero();
        foreach ($eventsTouched as $info) {
            /** @var UsageEvent $ev */
            $ev = $info['event'];
            $oldSum = $info['oldAmount'];
            $newPrice = $newPriceIds[(int) ($ev->priceId() ?: 0)];
            if ($newPrice === null) {
                // event.priceId не в карте — это может быть старый id,
                // который у нас был «до» replace. По условию reprice
                // заменяет строки, event.priceId может старым остаться.
                // Ищем новую цену по model + ts.
                $modelFor = $this->priceBook->modelFor($ev->provider(), $ev->model());
                if ($modelFor === null) {
                    continue;
                }
                $newPrice = $this->priceBook->effectivePrice($modelFor, $ev->ts());
                if ($newPrice === null) {
                    continue;
                }
            }
            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $ev->provider()]);
            $newCost = $this->calculator->calculate(
                $ev->tokensInput(),
                $ev->tokensOutput(),
                $ev->tokensCacheRead(),
                $ev->tokensCacheWrite(),
                $this->toPriceSheet($newPrice),
                $this->buildOffPeak($provider),
                $ev->ts(),
            );
            $newStr = $newCost->format(6);
            $newId = $newPrice->id();
            $ev->applyReprice($newStr, $newId);
            $rating = UsageRating::create($ev, $newStr, $newId, UsageRating::SOURCE_REPRICE);
            $rating->assign($ev);
            $this->em->persist($rating);
            $delta = $delta->add($newCost)->subtract($oldSum);
            $n++;
        }
        $this->em->flush();

        return ['n' => $n, 'deltaUsd' => $delta];
    }

    private function toPriceSheet(Price $p): PriceSheet
    {
        $rates = [];
        foreach ($p->rates() as $k => $v) {
            $rates[$k] = Money::of($v);
        }
        return new PriceSheet($p->currency(), $p->unit(), $rates);
    }

    private function buildOffPeak(?Provider $p): ?OffPeakWindow
    {
        if ($p === null || !$p->hasOffPeak()) {
            return null;
        }
        return OffPeakWindow::fromParts(
            (int) $p->offPeakStart(),
            (int) $p->offPeakEnd(),
            Money::of((string) $p->offPeakMultiplier()),
        );
    }
}
