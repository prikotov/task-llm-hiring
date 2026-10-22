<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\BillingCalculator;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Repository\PriceRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (повторные попытки для unrated).
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingCalculator $calculator,
        private readonly UsageEventRepository $events,
        private readonly PriceRepository $prices,
    ) {
    }

    /**
     * @return array{rated: int, unrated: int, unratedLines: string[]}
     */
    public function rateAll(): array
    {
        /** @var UsageEvent[] $pending */
        $pending = $this->events->findUnrated();

        $rated = 0;
        $unrated = 0;
        $unratedLines = [];

        foreach ($pending as $event) {
            $price = $this->prices->findApplicable(
                $event->getProvider(),
                $event->getModel(),
                $event->getTs(),
            );

            if ($price === null) {
                $event->markUnrated();
                ++$unrated;
                $unratedLines[] = sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->getProvider(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format('Y-m-d\TH:i:s\Z'),
                );

                continue;
            }

            $cost = $this->calculator->calculate(
                $event->getTokensInput(),
                $event->getTokensOutput(),
                $event->getTokensCacheRead(),
                $event->getTokensCacheWrite(),
                $price->getUnit(),
                $price->getRates(),
                $price->getProvider()->offPeak(),
                $event->getTs(),
            );

            $event->markRated($cost, $price->getCurrency(), $price->getValidFrom());
            ++$rated;
        }

        $this->em->flush();

        return ['rated' => $rated, 'unrated' => $unrated, 'unratedLines' => $unratedLines];
    }

    /**
     * Пересчёт стоимости одного события по строке цен (используется rate и reprice).
     */
    public function recalculate(UsageEvent $event, Price $price): string
    {
        return $this->calculator->calculate(
            $event->getTokensInput(),
            $event->getTokensOutput(),
            $event->getTokensCacheRead(),
            $event->getTokensCacheWrite(),
            $price->getUnit(),
            $price->getRates(),
            $price->getProvider()->offPeak(),
            $event->getTs(),
        );
    }
}
