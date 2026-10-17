<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\Entity\PriceRow;
use App\Domain\Billing\Entity\Provider;
use App\Domain\Billing\Entity\UsageEvent;
use App\Domain\Billing\Pricing\BillingCalculator;
use App\Domain\Billing\Pricing\OffPeak;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий.
 *
 * Выбор цены: строка цен модели события с наибольшим valid_from ≤ ts
 * (valid_from включительно). Без такой строки событие остаётся unrated —
 * каждый запуск app:billing:rate повторяет попытку по всем unrated.
 *
 * События рассчитываются ценой того момента, когда цена стала активной
 * (ts события), а не «текущей». Результат — стоимость события (округлённая
 * до 6 знаков HALF_UP) и ссылка на применённую строку (valid_from).
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingCalculator $calculator,
    ) {
    }

    /**
     * @return array{rated: int, unrated: list<UsageEvent>}
     */
    public function rateUnrated(): array
    {
        $unrated = $this->em->getRepository(UsageEvent::class)->findBy(['rated' => false]);

        $providersCache = [];

        $this->em->wrapInTransaction(function () use ($unrated, &$providersCache): void {
            foreach ($unrated as $event) {
                $row = $this->findPriceRow($event);
                if ($row === null) {
                    continue;
                }

                $offPeak = $this->providerOffPeak($event->provider, $providersCache);
                $cost = $this->calculator->rate($event, $row, $offPeak);
                $event->markRated($row->validFrom, $cost);
            }

            $this->em->flush();
        });

        $remaining = $this->em->getRepository(UsageEvent::class)->findBy(['rated' => false]);

        return [
            'rated' => \count($unrated) - \count($remaining),
            'unrated' => $remaining,
        ];
    }

    private function findPriceRow(UsageEvent $event): ?PriceRow
    {
        /** @var PriceRow[] $rows */
        $rows = $this->em->getRepository(PriceRow::class)->findBy([
            'provider' => $event->provider,
            'model' => $event->model,
        ]);

        $best = null;
        foreach ($rows as $row) {
            if ($row->validFrom > $event->ts) {
                continue;
            }
            if ($best === null || $row->validFrom > $best->validFrom) {
                $best = $row;
            }
        }

        return $best;
    }

    /** @param array<string, ?OffPeak> $cache */
    private function providerOffPeak(string $provider, array &$cache): ?OffPeak
    {
        if (!\array_key_exists($provider, $cache)) {
            $entity = $this->em->getRepository(Provider::class)->findOneBy(['code' => $provider]);
            $cache[$provider] = $entity !== null ? $entity->getOffPeak() : null;
        }

        return $cache[$provider];
    }
}
