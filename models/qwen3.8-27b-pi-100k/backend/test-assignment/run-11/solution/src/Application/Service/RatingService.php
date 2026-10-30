<?php

declare(strict_types=1);

namespace App\Application\Service;

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
 * Тарификация событий: перебирает все pending/retryable события,
 * выбирает действующую цену и записывает стоимость.
 *
 * Идемпотентность: event, уже тарифицированный (cost_usd не null),
 * пропускается. Unrated повторно оценивается на каждом rate-запуске
 * (TASK.md п. 7).
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceBookService $priceBook,
        private readonly RateCalculator $calculator = new RateCalculator(),
    ) {
    }

    /**
     * @return array{rated:int,unrated:int,unratedLines:string[]}
     */
    public function rate(): array
    {
        $rated = 0;
        $unratedLines = [];

        $q = $this->em->createQueryBuilder();
        $q->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status IN (:a, :b)')
            ->setParameter('a', UsageEvent::STATUS_PENDING)
            ->setParameter('b', UsageEvent::STATUS_RETRYABLE)
            ->orderBy('e.id', 'ASC');

        foreach ($q->getQuery()->getResult() as $ev) {
            /** @var UsageEvent $ev */
            $price = $this->selectPriceFor($ev);
            if ($price === null) {
                $ev->tryMarkRetryable();
                $unratedLines[] = sprintf(
                    'UNRATED %s/%s %s no_price',
                    $ev->provider(),
                    $ev->providerRequestId(),
                    $ev->ts()->format('c')
                );
                continue;
            }

            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $ev->provider()]);
            $cost = $this->calculator->calculate(
                $ev->tokensInput(),
                $ev->tokensOutput(),
                $ev->tokensCacheRead(),
                $ev->tokensCacheWrite(),
                $this->toPriceSheet($price),
                $this->buildOffPeak($provider),
                $ev->ts(),
            );
            $costStr = $cost->format(6);
            $ev->markRated($costStr, $price->id());
            $rating = UsageRating::create($ev, $costStr, $price->id(), UsageRating::SOURCE_RATE);
            $rating->assign($ev);
            $this->em->persist($rating);
            $rated++;
        }

        $this->em->flush();

        return [
            'rated' => $rated,
            'unrated' => count($unratedLines),
            'unratedLines' => $unratedLines,
        ];
    }

    private function selectPriceFor(UsageEvent $ev): ?Price
    {
        $model = $this->priceBook->modelFor($ev->provider(), $ev->model());
        if ($model === null) {
            return null;
        }
        return $this->priceBook->effectivePrice($model, $ev->ts());
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
