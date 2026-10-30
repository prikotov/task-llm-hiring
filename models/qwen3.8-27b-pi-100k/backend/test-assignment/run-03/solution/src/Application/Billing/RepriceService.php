<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Catalog\CatalogFileLoader;
use App\Domain\Billing\CostCalculator;
use App\Domain\Catalog\Model;
use App\Domain\Catalog\OffPeak;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Usage\EventStatus;
use App\Domain\Usage\UsageEvent;
use App\Shared\InputFormatException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * 1) Строки коррекции применяются upsert-ом по (provider, model, valid_from);
 *    провайдер и модель обязаны существовать в БД (секции providers/models
 *    файла коррекции игнорируются — они «могут отсутствовать или быть пустыми»,
 *    а домен требует, чтобы ссылка разрешалась по справочнику).
 *    Существующая строка меняется на месте (id сохраняется).
 * 2) Пересчитываются ТОЛЬКО события, тарифицировавшиеся заменёнными строками
 *    (у событий хранится ссылка price_row_id): формула та же, что при
 *    первичном расчёте (та же цена по видам токенов + оф-пик по ts), но со
 *    новыми ставками. Уже рассчитанные, но на другие строки, события не трогаются.
 * 3) Дельта — новая минус старая сумма стоимости пересчитанных событий.
 *
 * Итоговый app:billing:report показывает пересчитанные суммы без задвоений.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CostCalculator $calculator = new CostCalculator(),
    ) {
    }

    public function reprice(string $path): RepriceSummary
    {
        $file = (new CatalogFileLoader(true))->load($path);

        return $this->em->wrapInTransaction(function () use ($file): RepriceSummary {
            /** @var array<int, PriceRow> $touchedRows id => строка в окончательном виде */
            $touchedRows = [];

            foreach ($file->prices as $spec) {
                $row = $this->applyCorrection($spec->provider, $spec->model, $spec->validFrom, $spec->currency, $spec->unit, $spec->rates);
                $touchedRows[$row->id()] = $row;
            }

            $calculator = $this->calculator;
            $repricedEvents = 0;
            $deltaUsd = '0.000000';

            if (count($touchedRows) > 0) {
                /** @var array<string, OffPeak|null> $offPeakCache */
                $offPeakCache = [];
                $offPeakFor = function (string $providerCode) use (&$offPeakCache): ?OffPeak {
                    if (!\array_key_exists($providerCode, $offPeakCache)) {
                        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
                        $offPeakCache[$providerCode] = $provider?->offPeak();
                    }

                    return $offPeakCache[$providerCode];
                };

                /** @var UsageEvent[] $events */
                $events = $this->em->createQueryBuilder()
                    ->select('e')
                    ->from(UsageEvent::class, 'e')
                    ->where('e.status = :status')
                    ->andWhere('IDENTITY(e.priceRow) IN (:rowIds)')
                    ->setParameter('status', EventStatus::Rated)
                    ->setParameter('rowIds', array_keys($touchedRows))
                    ->getQuery()
                    ->getResult();

                foreach ($events as $event) {
                    $row = $event->priceRow();
                    if ($row === null) {
                        continue;
                    }

                    $oldCost = $event->costUsd();
                    $newCost = $calculator->calculate(
                        $event->tokensInput(),
                        $event->tokensOutput(),
                        $event->tokensCacheRead(),
                        $event->tokensCacheWrite(),
                        $row->unit(),
                        $row->rates(),
                        $offPeakFor($event->providerCode()),
                        $event->ts(),
                    );

                    ++$repricedEvents;
                    $deltaUsd = bcadd($deltaUsd, bcsub($newCost, (string) $oldCost, 6), 6);

                    if ($newCost !== $oldCost) {
                        $event->replaceCost($newCost);
                    }
                }
            }

            $this->em->flush();

            return new RepriceSummary($repricedEvents, $deltaUsd);
        });
    }

    private function applyCorrection(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $validFrom,
        string $currency,
        \App\Domain\Catalog\PricingUnit $unit,
        array $rates,
    ): PriceRow {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
        if ($provider === null) {
            throw new InputFormatException(sprintf(
                "коррекция (%s/%s, valid_from %s): провайдер \"%s\" отсутствует в загрузженном справочнике",
                $providerCode,
                $modelCode,
                $validFrom->format('Y-m-d\TH:i:s\Z'),
                $providerCode,
            ));
        }

        $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider->id(), 'code' => $modelCode]);
        if ($model === null) {
            throw new InputFormatException(sprintf(
                "коррекция (%s/%s, valid_from %s): модель \"%s\" отсутствует в загрузженном справочнике",
                $providerCode,
                $modelCode,
                $validFrom->format('Y-m-d\TH:i:s\Z'),
                $modelCode,
            ));
        }

        $row = $this->em->getRepository(PriceRow::class)->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
        if ($row === null) {
            $row = new PriceRow($model, $validFrom, $currency, $unit, $rates);
            $this->em->persist($row);
            $this->em->flush();

            return $row;
        }

        $row->replaceTerms($currency, $unit, $rates);

        return $row;
    }
}
