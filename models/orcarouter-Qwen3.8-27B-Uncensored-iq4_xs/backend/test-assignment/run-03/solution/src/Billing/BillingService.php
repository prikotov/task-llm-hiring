<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Model\Model;
use App\Domain\Model\PriceLine;
use App\Domain\Provider\Provider;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ядро учётной логики: выбор цены, расчёт стоимости события, отчёт,
 * ретро-коррекция цен.
 */
final class BillingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Тарифицирует все ещё не рассчитанные события.
     *
     * @return array{rated:int, unrated:UsageEvent[]}
     */
    public function ratePending(): array
    {
        $events = $this->em->getRepository(UsageEvent::class)->findBy(
            ['status' => UsageEvent::STATUS_UNRATED],
        );

        $rated = 0;
        /** @var UsageEvent[] $unrated */
        $unrated = [];

        foreach ($events as $event) {
            $provider = $this->findProviderByCode($event->getProvider());
            $line = $this->findPriceLine($event, $provider);

            if ($line === null) {
                $unrated[] = $event;

                continue;
            }

            $event->setCost($this->computeCost($event, $line, $provider));
            $event->setPriceLine($line);
            $event->setStatus(UsageEvent::STATUS_RATED);
            $rated++;
        }

        $this->em->flush();

        return ['rated' => $rated, 'unrated' => $unrated];
    }

    /**
     * Строит данные отчёта: группы по (user, provider/model) + TOTAL.
     *
     * @return array{rows:array<array{user:string,model:string,in:int,out:int,cr:int,cw:int,cost:string}>, total:array{in:int,out:int,cr:int,cw:int,cost:string}}
     */
    public function report(?string $from, ?string $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        /** @var UsageEvent[] $events */
        $events = $qb->getQuery()->getResult();

        /** @var array<string, array{user:string,model:string,in:int,out:int,cr:int,cw:int,cost:string}> $groups */
        $groups = [];
        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => Money::zero()];

        foreach ($events as $event) {
            $modelLabel = $event->getProvider().'/'.$event->getModel();
            $key = $event->getUser()."|".$modelLabel;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUser(),
                    'model' => $modelLabel,
                    'in' => 0,
                    'out' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => Money::zero(),
                ];
            }

            $groups[$key]['in'] += $event->getTokensInput();
            $groups[$key]['out'] += $event->getTokensOutput();
            $groups[$key]['cr'] += $event->getTokensCacheRead();
            $groups[$key]['cw'] += $event->getTokensCacheWrite();
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], (string) $event->getCost(), Money::MONEY_SCALE);

            $total['in'] += $event->getTokensInput();
            $total['out'] += $event->getTokensOutput();
            $total['cr'] += $event->getTokensCacheRead();
            $total['cw'] += $event->getTokensCacheWrite();
            $total['cost'] = bcadd($total['cost'], (string) $event->getCost(), Money::MONEY_SCALE);
        }

        usort($groups, static function (array $a, array $b): int {
            $byUser = strcmp($a['user'], $b['user']);
            if ($byUser !== 0) {
                return $byUser;
            }

            return strcmp($a['model'], $b['model']);
        });

        return [
            'rows' => array_values($groups),
            'total' => $total,
        ];
    }

    /**
     * Выбор цены: строка с наибольшим valid_from <= ts (включительно).
     */
    public function findPriceLine(UsageEvent $event, ?Provider $provider): ?PriceLine
    {
        $qb = $this->em->createQueryBuilder()
            ->select('pl')
            ->from(PriceLine::class, 'pl')
            ->join('pl.model', 'm')
            ->where('m.provider = :provider')
            ->andWhere('m.code = :code')
            ->andWhere('pl.validFrom <= :ts')
            ->orderBy('pl.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('code', $event->getModel())
            ->setParameter('ts', $event->getTs());

        if ($provider !== null) {
            $qb->setParameter('provider', $provider);
        } else {
            return null;
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Точная стоимость события до финального округления.
     *
     * @return string decimal, без округления до 6 знаков (с запасом точности)
     */
    public function computeCost(UsageEvent $event, PriceLine $line, ?Provider $provider): string
    {
        $rates = $line->getRates();
        $scale = Money::CALC_SCALE;

        if ($line->getUnit() === PriceLine::UNIT_UNIFIED) {
            $totalTokens = $event->getTokensInput()
                + $event->getTokensOutput()
                + $event->getTokensCacheRead()
                + $event->getTokensCacheWrite();
            $sum = bcmul((string) $totalTokens, $this->normalizeRate($rates['any'] ?? '0'), $scale);
        } else {
            $sum = bcadd(
                bcadd(
                    bcmul((string) $event->getTokensInput(), $this->normalizeRate($rates['input'] ?? '0'), $scale),
                    bcmul((string) $event->getTokensOutput(), $this->normalizeRate($rates['output'] ?? '0'), $scale),
                    $scale,
                ),
                bcadd(
                    bcmul((string) $event->getTokensCacheRead(), $this->normalizeRate($rates['cache_read'] ?? '0'), $scale),
                    bcmul((string) $event->getTokensCacheWrite(), $this->normalizeRate($rates['cache_write'] ?? '0'), $scale),
                    $scale,
                ),
                $scale,
            );
        }

        $cost = bcdiv($sum, PriceLine::RATES_UNIT, $scale);

        if ($provider !== null) {
            $minutesOfDay = (int) $event->getTs()->format('H') * 60
                + (int) $event->getTs()->format('i');
            if ($provider->isOffPeak($minutesOfDay) && $provider->getOffPeakMultiplier() !== null) {
                $cost = bcmul($cost, Money::clean($provider->getOffPeakMultiplier()), $scale);
            }
        }

        return Money::roundHalfUp($cost, Money::MONEY_SCALE);
    }

    /**
     * Ретро-коррекция: применяет исправленные строки цен (upsert) и
     * пересчитывает события, тарифицировавшиеся заменёнными строками.
     *
     * @param array<array{provider:string,model:string,validFrom:\DateTimeImmutable,currency:string,unit:string,ratesJson:string}> $prices
     * @return array{count:int, delta:string}
     */
    public function reprice(array $prices): array
    {
        // 1. Определяем строки, которые уже есть в БД и будут заменены.
        $affectedLineIds = [];
        foreach ($prices as $p) {
            $provider = $this->findProviderByCode($p['provider']);
            if ($provider === null) {
                continue;
            }
            $model = $this->findModel($provider, $p['model']);
            if ($model === null) {
                continue;
            }
            $line = $this->findPriceLineByKey($provider, $model, $p['validFrom']);
            if ($line !== null) {
                $affectedLineIds[$line->getId()] = $line->getId();
            }
        }

        // 2. Собираем события, тарифицированные этими строками, и старую сумму.
        $events = [];
        $oldSum = Money::zero();
        if ($affectedLineIds !== []) {
            $qb = $this->em->createQueryBuilder()
                ->select('e')
                ->from(UsageEvent::class, 'e')
                ->where('e.status = :status')
                ->andWhere('e.priceLine IN (:ids)')
                ->setParameter('status', UsageEvent::STATUS_RATED)
                ->setParameter('ids', array_values($affectedLineIds));
            $events = $qb->getQuery()->getResult();

            foreach ($events as $event) {
                $oldSum = bcadd($oldSum, (string) $event->getCost(), Money::MONEY_SCALE);
            }
        }

        // 3. Применяем (upsert) новые строки.
        foreach ($prices as $p) {
            $this->upsertPriceLine(
                $p['provider'],
                $p['model'],
                $p['validFrom'],
                $p['currency'],
                $p['unit'],
                $p['ratesJson'],
            );
        }
        $this->em->flush();

        // 4. Пересчитываем события.
        $newSum = Money::zero();
        $count = 0;
        foreach ($events as $event) {
            /** @var UsageEvent $event fresh object after flush */
            $provider = $this->findProviderByCode($event->getProvider());
            $line = $this->findPriceLine($event, $provider);
            if ($line === null) {
                continue;
            }
            $event->setCost($this->computeCost($event, $line, $provider));
            $event->setPriceLine($line);
            $newSum = bcadd($newSum, $event->getCost(), Money::MONEY_SCALE);
            $count++;
        }
        $this->em->flush();

        return [
            'count' => $count,
            'delta' => Money::toSix(bcsub($newSum, $oldSum, Money::MONEY_SCALE)),
        ];
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    public function findProviderByCode(string $code): ?Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
    }

    public function findModel(Provider $provider, string $code): ?Model
    {
        return $this->em->getRepository(Model::class)->findOneBy([
            'provider' => $provider,
            'code' => $code,
        ]);
    }

    private function findPriceLineByKey(Provider $provider, Model $model, \DateTimeImmutable $validFrom): ?PriceLine
    {
        return $this->em->getRepository(PriceLine::class)->findOneBy([
            'provider' => $provider,
            'model' => $model,
            'validFrom' => $validFrom,
        ]);
    }

    /**
     * Upsert строки цен по (provider, model, valid_from).
     *
     * @return PriceLine|null null, если провайдер или модель отсутствуют в БД
     */
    private function upsertPriceLine(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        string $ratesJson,
    ): ?PriceLine {
        $provider = $this->findProviderByCode($providerCode);
        if ($provider === null) {
            return null;
        }
        $model = $this->findModel($provider, $modelCode);
        if ($model === null) {
            return null;
        }

        $line = $this->findPriceLineByKey($provider, $model, $validFrom);
        if ($line === null) {
            $line = new PriceLine();
            $line->setProvider($provider);
            $line->setModel($model);
            $line->setValidFrom($validFrom);
            $this->em->persist($line);
        }

        $line->setCurrency($currency);
        $line->setUnit($unit);
        $line->setRatesJson($ratesJson);

        return $line;
    }

    private function normalizeRate(string $rate): string
    {
        return Money::clean($rate);
    }
}
