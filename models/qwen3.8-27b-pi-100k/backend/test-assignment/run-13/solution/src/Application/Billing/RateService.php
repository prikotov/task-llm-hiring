<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Infrastructure\CostRecord;
use App\Infrastructure\Provider;
use App\Infrastructure\PriceRow;
use App\Infrastructure\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация unrated-событий.
 *
 * Идемпотентен: повторный запуск не создаёт дубликаты CostRecord.
 */
final readonly class RateService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PricingCalculator $calculator,
    ) {
    }

    /**
     * @return array{rated: int, unrated: int, unratedEvents: list<array{provider: string, requestId: string, ts: string}>}
     */
    public function rateAll(): array
    {
        $em = $this->em;

        $unrated = $em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_UNRATED)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var UsageEvent[] $unratedList */
        $unratedList = $unrated;

        $rated = 0;
        $unratedResult = 0;
        $unratedEvents = [];

        foreach ($unratedList as $event) {
            $eventId = $event->id();
            if ($eventId === null) {
                $unratedResult += 1;
                $unratedEvents[] = ['provider' => $event->providerValue(), 'requestId' => $event->requestIdValue(), 'ts' => $event->tsValue()->format('Y-m-d\TH:i:s\Z')];
                continue;
            }

            // Уже расценено?
            $existingCost = $em->createQueryBuilder()
                ->select('c')
                ->from(CostRecord::class, 'c')
                ->where('c.eventId = :eid')
                ->setParameter('eid', $eventId)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingCost !== null) {
                $event->markRated();
                $rated += 1;
                continue;
            }

            // Провайдер
            $provider = $em->getRepository(Provider::class)
                ->findOneBy(['code' => $event->providerValue()]);
            if ($provider === null) {
                $unratedResult += 1;
                $unratedEvents[] = ['provider' => $event->providerValue(), 'requestId' => $event->requestIdValue(), 'ts' => $event->tsValue()->format('Y-m-d\TH:i:s\Z')];
                continue;
            }

            // Строка цены: наибольшая validFromEpoch <= ts_epoch
            $tsEpoch = $event->tsValue()->getTimestamp();
            $row = $em->createQueryBuilder()
                ->select('p')
                ->from(PriceRow::class, 'p')
                ->where('p.provider = :prov')
                ->andWhere('p.model = :model')
                ->andWhere('p.validFromEpoch <= :epoch')
                ->setParameter('prov', $event->providerValue())
                ->setParameter('model', $event->modelValue())
                ->setParameter('epoch', $tsEpoch)
                ->orderBy('p.validFromEpoch', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($row === null) {
                $unratedResult += 1;
                $unratedEvents[] = ['provider' => $event->providerValue(), 'requestId' => $event->requestIdValue(), 'ts' => $event->tsValue()->format('Y-m-d\TH:i:s\Z')];
                continue;
            }

            // Расчёт
            $result = $this->calculator->eventCost($event, $row, $provider);
            $rateKey = PriceRow::rateKey(
                $event->providerValue(),
                $event->modelValue(),
                (int) $row->validFromEpochValue(),
            );

            $costRecord = new CostRecord(
                eventId: $eventId,
                provider: $event->providerValue(),
                model: $event->modelValue(),
                user: $event->userValue(),
                ts: $event->tsValue(),
                tokensInput: $event->tokensInputValue(),
                tokensOutput: $event->tokensOutputValue(),
                tokensCacheRead: $event->tokensCacheReadValue(),
                tokensCacheWrite: $event->tokensCacheWriteValue(),
                rateKey: $rateKey,
                rawCost: $result->rawCost,
                appliedCost: $result->appliedCost,
                originalCost: $result->roundedCost,
            );
            $em->persist($costRecord);

            $event->markRated();
            $rated += 1;
        }

        $em->flush();

        return [
            'rated' => $rated,
            'unrated' => $unratedResult,
            'unratedEvents' => $unratedEvents,
        ];
    }
}
