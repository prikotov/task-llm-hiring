<?php

declare(strict_types=1);

namespace App\Domain;

use App\Entity\EventRating;
use App\Domain\CostCalculator;
use App\Repository\PriceRowRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки
 * unrated — цены могли подтянуться позже).
 *
 * Правила:
 *  - цена — строка цен модели с наибольшим valid_from ≤ ts (valid_from включительно),
 *    иначе событие остаётся unrated (не ошибка);
 *  - стоимость события = расчёт по схеме + (опционально) оф-пик провайдера,
 *    затем одно HALF_UP-округление до 6 знаков;
 *  - результат фиксируется строкой event_ratings: повторный прогон событий
 *    не пересчитывает и не задваивает.
 */
final class RatingRunner
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $usageEventRepository,
        private readonly ProviderRepository $providerRepository,
        private readonly PriceRowRepository $priceRowRepository,
    ) {
    }

    public function rateAll(): RatingSummary
    {
        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            $events = $this->usageEventRepository->findUnrated();
            $unrated = [];
            $rated = 0;
            $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

            foreach ($events as $event) {
                $priceRow = $this->priceRowRepository->findActiveAt(
                    $event->getProviderCode(),
                    $event->getModelCode(),
                    $event->getTs(),
                );

                if ($priceRow === null) {
                    $unrated[] = new UnratedEvent($event->getProviderCode(), $event->getProviderRequestId(), $event->getTs());

                    continue;
                }

                $offPeak = null;
                $provider = $this->providerRepository->findByCode($event->getProviderCode());
                if ($provider !== null && $provider->hasOffPeak()) {
                    $offPeak = $provider->offPeakWindow();
                }

                $cost = CostCalculator::eventCost(
                    $priceRow->getUnit(),
                    $priceRow->getRates(),
                    $event->getTokensInput(),
                    $event->getTokensOutput(),
                    $event->getTokensCacheRead(),
                    $event->getTokensCacheWrite(),
                    $offPeak,
                    CostCalculator::minuteOfDayUtc($event->getTs()),
                );

                $this->em->persist(new EventRating($event, $priceRow, $priceRow->getCurrency(), $cost, $now));
                ++$rated;
            }

            $this->em->flush();
            $conn->commit();

            return new RatingSummary($rated, $unrated);
        } catch (Throwable $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }

            if ($e instanceof \InvalidArgumentException || $e instanceof \ValueError) {
                throw $e;
            }

            throw new \RuntimeException('Тарификация завершена ошибкой: ' . $e->getMessage(), 1, $e);
        }
    }
}
