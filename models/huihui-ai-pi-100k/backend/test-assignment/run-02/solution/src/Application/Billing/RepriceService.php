<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Prices\PriceFileSpec;
use App\Application\Prices\ProviderSpec;
use App\Domain\Billing\PriceCorrection;
use App\Domain\Billing\RateCalculator;
use App\Domain\Pricing\Model;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\Provider;
use App\Domain\Shared\Decimal;
use App\Domain\Shared\TimeOfDay;
use App\Domain\Usage\UsageStatus;
use App\Exception\InvalidRepriceFileException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус задание `app:billing:reprice`).
 *
 * Механизм:
 *  1. Секция `providers` (если есть) применяется upsert'ом к уже
 *     существующим провайдерам (code обязан существовать в базе);
 *     новые провайдеры не создаются.
 *  2. Секции `models` используются как проверка: каждая пара
 *     (provider, code) должна существовать в базе (не только в файле).
 *  3. Строки цен upsert'ятся по (provider, model, valid_from) по уже
 *     загруженному справочнику; провайдер и модель обязаны существовать
 *     в базе.
 *  4. Пересчитываются все уже rated-события, чья строка цен либо
 *     заменена п. 3, либо принадлежит провайдеру из секции `providers`
 *     (оф-пик тоже влияет на стоимость). Пересчёт использует новую строку
 *     и текущую политику оф-пик провайдера; для каждого события в журнал
 *     `price_corrections` пишется пара «старая/новая стоимость».
 *  5. Стоимость в `usage_events` пересчитывается на месте — отчёт
 *     показывает новые суммы без задвоений, unrated-события не трогаются.
 *
 * @return array{events:int, delta_usd:string}
 *
 * @throws InvalidRepriceFileException
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RateCalculator $calculator,
    ) {
    }

    public function reprice(PriceFileSpec $spec): array
    {
        $em = $this->em;

        return $em->getConnection()->transactional(function () use ($em, $spec): array {
            $providerCache = self::upsertProviders($em, $spec);
            self::validateModels($em, $spec, $providerCache);

            $affectedRows = self::upsertPriceRows($em, $spec, $providerCache);

            // Секция providers изменила оф-пик — стоимость зависит не только от строк цен,
            // поэтому затрагиваются все строки цен этих провайдеров.
            if ($spec->providers !== []) {
                $codes = array_map(
                    static fn (ProviderSpec $p): string => $p->code,
                    $spec->providers,
                );
                /** @var PriceRow[] $providerRows */
                $providerRows = $em
                    ->createQuery(
                        'SELECT DISTINCT r FROM '.PriceRow::class.' r
                         JOIN r.provider p
                         WHERE p.code IN (:codes)',
                    )
                    ->setParameter('codes', $codes)
                    ->getResult();
                foreach ($providerRows as $row) {
                    $affectedRows[(int) $row->getId()] = $row;
                }
            }

            /** @var \App\Domain\Usage\UsageEvent[] $events */
            $events = $affectedRows === []
                ? []
                : $em->createQuery(
                    'SELECT e FROM App\Domain\Usage\UsageEvent e
                     WHERE e.status = :status
                       AND e.priceRow IN (SELECT r.id FROM App\Domain\Pricing\PriceRow r WHERE r.id IN (:rows))'
                )
                    ->setParameter('status', UsageStatus::Rated)
                    ->setParameter('rows', array_keys($affectedRows))
                    ->getResult();

            $count = 0;
            $oldSum = '0';
            $newSum = '0';

            foreach ($events as $event) {
                $row = $affectedRows[$event->getPriceRow()->getId()];
                $oldCost = $event->getCost() ?? '0';
                $newCost = $this->calculator->eventCost(
                    $event->getTokenCounts(),
                    $row,
                    $event->getProvider()->offPeakPolicy(),
                    TimeOfDay::minutesOfDayUtc($event->getTs()),
                );

                $event->changeRatedCost($newCost);
                $em->persist(new PriceCorrection($event, $row, $oldCost, $newCost));

                $count++;
                $oldSum = Decimal::add($oldSum, $oldCost);
                $newSum = Decimal::add($newSum, $newCost);
            }

            $em->flush();

            return [
                'events' => $count,
                'delta_usd' => Decimal::sub($newSum, $oldSum),
            ];
        });
    }

    /**
     * @param array<string, Provider> $providerCache
     *
     * @return array<string, Provider>
     */
    private static function upsertProviders(EntityManagerInterface $em, PriceFileSpec $spec): array
    {
        $cache = [];
        foreach ($spec->providers as $p) {
            $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $p->code]);
            if ($provider === null) {
                throw new InvalidRepriceFileException(sprintf('provider "%s" not found in the loaded reference data', $p->code));
            }
            $provider->update(
                $p->name,
                $p->offPeak?->startMinutes,
                $p->offPeak?->endMinutes,
                $p->offPeak?->multiplier,
            );
            $cache[$p->code] = $provider;
        }

        return $cache;
    }

    /**
     * Модели из файла должны существовать в базе (провайдер — тоже).
     *
     * @param array<string, Provider> $providerCache
     */
    private static function validateModels(EntityManagerInterface $em, PriceFileSpec $spec, array $providerCache): void
    {
        foreach ($spec->models as $m) {
            self::resolveModel($em, $m->provider, $m->code, $providerCache);
        }
    }

    /**
     * @param array<string, Provider> $providerCache
     *
     * @return array<int, PriceRow> id строки => сущность
     */
    private static function upsertPriceRows(EntityManagerInterface $em, PriceFileSpec $spec, array $providerCache): array
    {
        $affected = [];
        foreach ($spec->priceRows as $r) {
            $provider = self::resolveProvider($em, $r->provider, $providerCache);
            $model = self::resolveModel($em, $r->provider, $r->model, $providerCache);

            $row = $em->getRepository(PriceRow::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $r->validFrom,
            ]);

            if ($row === null) {
                $row = new PriceRow(
                    $provider,
                    $model,
                    $r->validFrom,
                    $r->currency,
                    $r->unit,
                    $r->rates['input'] ?? null,
                    $r->rates['output'] ?? null,
                    $r->rates['cache_read'] ?? null,
                    $r->rates['cache_write'] ?? null,
                    $r->rates['any'] ?? null,
                );
                $em->persist($row);
            } else {
                $row->replaceRates($r->currency, $r->unit, $r->rates);
            }

            $affected[$row->getId() ?? 0] = $row;
        }

        $em->flush(); // id уже назначен — для коллекции затронутых строк

        return $affected;
    }

    /**
     * @param array<string, Provider> $cache
     */
    private static function resolveProvider(EntityManagerInterface $em, string $code, array $cache): Provider
    {
        if (isset($cache[$code])) {
            return $cache[$code];
        }

        $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($provider === null) {
            throw new InvalidRepriceFileException(sprintf('provider "%s" not found in the loaded reference data', $code));
        }

        $cache[$code] = $provider;

        return $provider;
    }

    /**
     * @param array<string, Provider> $cache
     */
    private static function resolveModel(EntityManagerInterface $em, string $providerCode, string $modelCode, array $cache): Model
    {
        $provider = self::resolveProvider($em, $providerCode, $cache);
        $model = $em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
        if ($model === null) {
            throw new InvalidRepriceFileException(sprintf('model "%s/%s" not found in the loaded reference data', $providerCode, $modelCode));
        }

        return $model;
    }
}
