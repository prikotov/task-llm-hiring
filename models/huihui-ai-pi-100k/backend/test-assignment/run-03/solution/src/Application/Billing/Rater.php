<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Common\Iso8601Utc as Utc;
use App\Domain\Pricing\CostCalculator;
use App\Persistence\Entity\Provider;
use App\Persistence\Entity\UsageEvent;
use App\Persistence\Enum\EventStatus;
use App\Persistence\Repository\PriceRowRepository;
use App\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (п. 1, 7 TASK.md).
 *
 * Каждый запуск проходит все pending/unrated события: unrated — это не
 * ошибка, при следующем запуске повторятся все попытки (цены могли
 * подтянуться позже). Событие получает строку цен с наибольшим
 * valid_from ≤ ts (valid_from включительно); иначе остаётся unrated.
 */
final class Rater
{
    private const CHUNK_SIZE = 500;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly PriceRowRepository $priceRows,
        private readonly CostCalculator $calculator,
    ) {
    }

    public function rateAll(): RateResult
    {
        return $this->em->wrapInTransaction(function (): RateResult {
            $rated = 0;
            /** @var list<UsageEvent> $unrated */
            $unrated = [];
            $afterId = 0;

            while (true) {
                $chunk = $this->events->findNotRated(self::CHUNK_SIZE, $afterId);
                if ($chunk === []) {
                    break;
                }
                // id в чанке отсортированы по возрастанию: последняя — максимальная.
                $afterId = $chunk[count($chunk) - 1]->id;

                $rowsByPair = [];
                $providerByCode = [];
                foreach ($chunk as $event) {
                    $pair = $event->provider."\0".$event->model;
                    if (!isset($rowsByPair[$pair])) {
                        $rowsByPair[$pair] = $this->priceRows->findAllFor($event->provider, $event->model);
                    }
                    if (!array_key_exists($event->provider, $providerByCode)) {
                        $providerByCode[$event->provider] = $this->em->getRepository(Provider::class)->findOneBy(['code' => $event->provider]);
                    }
                }

                foreach ($chunk as $event) {
                    $row = $this->chooseRow($rowsByPair[$event->provider."\0".$event->model], $event);
                    if ($row === null) {
                        $event->status = EventStatus::Unrated;
                        $unrated[] = $event;
                        continue;
                    }

                    $event->status = EventStatus::Rated;
                    $event->priceRowId = $row->id;
                    $event->billedCost = $this->calculator->eventCost(
                        $event->tokenUsage(),
                        $row->toValueObject(),
                        $event->tsUtc(),
                        $providerByCode[$event->provider]?->offPeak(),
                    );
                    $rated++;
                }

                $this->em->flush();
            }

            return new RateResult($rated, count($unrated), $unrated);
        });
    }

    /** Строка с наибольшим valid_from ≤ ts (включительно); null — цены ещё нет. */
    private function chooseRow(array $rowsAsc, UsageEvent $event): ?\App\Persistence\Entity\PriceRow
    {
        $chosen = null;
        foreach ($rowsAsc as $row) {
            if (Utc::reanchor($row->validFrom) > $event->tsUtc()) {
                break;
            }
            $chosen = $row;
        }

        return $chosen;
    }
}
