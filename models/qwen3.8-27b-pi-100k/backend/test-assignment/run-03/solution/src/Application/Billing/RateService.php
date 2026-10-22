<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\CostCalculator;
use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Usage\EventStatus;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий (pending и unrated —
 * повторная попытка, цены могли подтянуться позже).
 *
 * Выбор цены: строка своей модели с наибольшим valid_from ≤ ts события
 * (valid_from включительно). Строка цены фиксируется на событии — это
 * то, что делает возможной ретро-коррекцию (app:billing:reprice).
 */
final class RateService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CostCalculator $calculator = new CostCalculator(),
    ) {
    }

    public function rate(): RateSummary
    {
        return $this->em->wrapInTransaction(function (): RateSummary {
            /** @var UsageEvent[] $events */
            $events = $this->em->createQueryBuilder()
                ->select('e')
                ->from(UsageEvent::class, 'e')
                ->where('e.status IN (:statuses)')
                ->setParameter('statuses', [EventStatus::Pending->value, EventStatus::Unrated->value])
                ->getQuery()
                ->getResult();

            // Группируем по (provider, model): справочник и цены грузим один раз
            // на группу, а не на каждое событие.
            /** @var array<string, array<string, UsageEvent[]>> $groups */
            $groups = [];
            foreach ($events as $event) {
                $groups[$event->providerCode()][$event->modelCode()][] = $event;
            }

            $rated = 0;
            $unrated = [];

            foreach ($groups as $providerCode => $models) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);

                foreach ($models as $modelCode => $group) {
                    $rows = $this->priceCandidates($provider, $modelCode);

                    foreach ($group as $event) {
                        $row = self::pickRow($rows, $event->ts());
                        if ($row === null) {
                            $event->markUnrated();
                            $unrated[] = $event;
                            continue;
                        }

                        $cost = $this->calculator->calculate(
                            $event->tokensInput(),
                            $event->tokensOutput(),
                            $event->tokensCacheRead(),
                            $event->tokensCacheWrite(),
                            $row->unit(),
                            $row->rates(),
                            $provider?->offPeak(),
                            $event->ts(),
                        );

                        $event->markRated($row, $cost);
                        ++$rated;
                    }
                }
            }

            $this->em->flush();

            return new RateSummary($rated, $unrated);
        });
    }

    /**
     * Строки цены модели, отсортированные по valid_from DESC.
     *
     * @return PriceRow[]
     */
    private function priceCandidates(?Provider $provider, string $modelCode): array
    {
        if ($provider === null) {
            return [];
        }

        $model = $this->em->getRepository(Model::class)->findOneBy([
            'provider' => $provider->id(),
            'code' => $modelCode,
        ]);

        return $model === null
            ? []
            : $this->em->getRepository(PriceRow::class)->findBy(['model' => $model], ['validFrom' => 'DESC']);
    }

    /**
     * @param PriceRow[] $rows DESC по valid_from
     */
    private static function pickRow(array $rows, \DateTimeImmutable $ts): ?PriceRow
    {
        foreach ($rows as $row) {
            if ($row->validFrom() <= $ts) {
                return $row;
            }
        }

        return null;
    }
}
