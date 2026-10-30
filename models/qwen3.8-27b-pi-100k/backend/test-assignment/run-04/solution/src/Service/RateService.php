<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\OffPeak;
use App\Domain\PriceSpec;
use App\Domain\PricingCalculator;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий: выбирает для каждого pending-события строку цен его
 * модели с наибольшим valid_from ≤ ts события (включительно), считает точную
 * стоимость (bcmath), применяет оф-пик провайдера и округляет итог до 6 знаков HALF_UP.
 *
 * Каждый запуск повторяет попытку для всех ещё не рассчитанных событий
 * (цены могли появиться позже, чем события).
 */
final class RateService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricingCalculator $calculator,
    ) {}

    /**
     * @psalm-return array{rated: int, unrated: list<UsageEvent>}
     */
    public function rateAllPending(): array
    {
        $priceIndex = $this->buildPriceIndex(); // "provider|model" => list<PriceRow>, по validFrom убывающе
        $offPeakByProvider = $this->buildOffPeakMap();

        /** @var list<UsageEvent> $pending */
        $pending = $this->em->getRepository(UsageEvent::class)
            ->findBy(['status' => UsageEvent::STATUS_PENDING]);

        $rated = 0;
        $unrated = [];
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        foreach ($pending as $event) {
            $row = $this->choosePriceRow(
                $priceIndex[sprintf('%s|%s', $event->provider, $event->model)] ?? [],
                $event->ts,
            );

            if ($row === null) {
                $unrated[] = $event;
                continue;
            }

            $spec = new PriceSpec($row->unit, $row->rates);
            $cost = $this->calculator->rate(
                $spec,
                $event->tokensInput,
                $event->tokensOutput,
                $event->tokensCacheRead,
                $event->tokensCacheWrite,
                $offPeakByProvider[$event->provider] ?? null,
                $event->ts,
            );

            $event->markRated((int) $row->id, $cost, $now);
            $rated++;
        }

        $this->em->flush();

        return ['rated' => $rated, 'unrated' => $unrated];
    }

    /**
     * Строка с наибольшим valid_from ≤ eventTs (включительно).
     *
     * @param list<PriceRow> $rowsFromLatest — отсортированы по validFrom убывающе
     */
    private function choosePriceRow(array $rows, \DateTimeImmutable $eventTs): ?PriceRow
    {
        foreach ($rows as $row) {
            if ($row->validFrom <= $eventTs) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @return array<string, ?OffPeak> provider code => окно (NULL — без скидки)
     */
    private function buildOffPeakMap(): array
    {
        $map = [];
        /** @var Provider $p */
        foreach ($this->em->getRepository(Provider::class)->findAll() as $p) {
            $map[$p->code] = ($p->offPeakStart !== null && $p->offPeakEnd !== null && $p->offPeakMultiplier !== null)
                ? OffPeak::create($p->offPeakStart, $p->offPeakEnd, $p->offPeakMultiplier)
                : null;
        }

        return $map;
    }

    /**
     * @return array<string, list<PriceRow>> "provider|model" => строки цен по validFrom desc
     */
    private function buildPriceIndex(): array
    {
        $index = [];
        /** @var PriceRow $row */
        foreach ($this->em->getRepository(PriceRow::class)->findAll() as $row) {
            $key = sprintf('%s|%s', $row->model->provider->code, $row->model->code);
            $index[$key][] = $row;
        }

        foreach ($index as &$rows) {
            usort(
                $rows,
                static fn (PriceRow $a, PriceRow $b): int => $b->validFrom <=> $a->validFrom,
            );
        }

        return $index;
    }
}
