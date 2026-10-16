<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Import\PriceFile;
use App\Import\PriceImporter;
use App\Support\Decimals;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий.
 *
 * Правила (ТЗ):
 *  - выбирается строка цены модели с наибольшим valid_from <= ts (включительно);
 *  - если такой строки нет — событие остаётся unrated и в каждом последующем
 *    запуске rate повторяется попытка (цены могли подтянуться позже);
 *  - unrated — не ошибка, в отчёт такие события не попадают;
 *  - рассчитывается каждое событие ровно один раз: повторный запуск
 *    не пересчитывает уже rated-события (изменение стоимости возможно
 *    только через reprice).
 */
final class RateEngine
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * Тарифицирует все ещё не рассчитанные события.
     *
     * @return array{rated: int, unrated: list<array{provider: string, requestId: string, ts: string}>}
     */
    public function rateAll(): array
    {
        $rated = 0;
        /** @var list<array{provider: string, requestId: string, ts: string}> $unrated */
        $unrated = [];

        $this->em->getConnection()->transactional(
            function () use (&$rated, &$unrated): void {
                /** @var UsageEvent[] $pending */
                $pending = $this->em->getRepository(UsageEvent::class)->findBy(
                    ['status' => UsageEvent::STATUS_PENDING],
                    ['id' => 'ASC'],
                );

                foreach ($pending as $event) {
                    $context = $this->lookup($event);
                    if ($context === null) {
                        $unrated[] = [
                            'provider' => $event->getProvider(),
                            'requestId' => $event->getProviderRequestId(),
                            'ts' => $event->getTs()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
                        ];

                        continue;
                    }

                    $result = $this->calculator->calculate(
                        $context['row'],
                        $event->getTokens(),
                        $context['offPeak'],
                        $event->getTs(),
                    );
                    $event->markRated(
                        $context['row']->getId(),
                        $result['baseCost'],
                        $result['offPeakApplied'],
                        $result['costUsd'],
                    );
                    $rated++;
                }

                $this->em->flush();
            },
        );

        return ['rated' => $rated, 'unrated' => $unrated];
    }

    /**
     * Строка цены события + оф-пик его провайдера.
     *
     * @return array{row: PriceRow, offPeak: OffPeak|null}|null
     */
    private function lookup(UsageEvent $event): ?array
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $event->getProvider()]);
        if ($provider === null) {
            return null;
        }

        $query = $this->em->createQueryBuilder()
            ->select('pr')
            ->from(PriceRow::class, 'pr')
            ->innerJoin(\App\Entity\Model::class, 'm', 'WITH', 'm.id = pr.modelId')
            ->innerJoin(Provider::class, 'p', 'WITH', 'p.id = m.providerId')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('pr.validFrom <= :ts')
            ->setParameter('provider', $event->getProvider())
            ->setParameter('model', $event->getModel())
            ->setParameter('ts', $event->getTs())
            ->orderBy('pr.validFrom', 'DESC')
            ->addOrderBy('pr.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $row = $query->getOneOrNullResult();

        if ($row === null) {
            return null;
        }

        return ['row' => $row, 'offPeak' => $provider->getOffPeak()];
    }
}
