<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\RepriceSummary;
use App\Billing\OffPeak\OffPeakPricing;
use App\Billing\Pricing\PriceUnit;
use App\Billing\Pricing\PricingCalculator;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\RepriceEventCorrection;
use App\Entity\RepriceRun;
use App\Entity\UsageEvent;
use App\Shared\Exception\BadInputException;
use App\Shared\Math\Decimal;
use App\Support\JsonFile;
use App\Support\SpecReader;
use App\Support\StrictIsoUtc;
use Doctrine\ORM\EntityManagerInterface;

/**
 * app:billing:reprice — ретро-коррекция цены.
 *
 * Файл коррекции — JSON того же формата, что и история цен, но используется
 * только секция `prices` со исправленными строками: провайдер и модель
 * каждой строки должны существовать в базе, а не в самом файле (секции
 * `providers`/`models` в файле быть не обязаны).
 *
 * Действия (одна транзакция):
 *  1) upsert строк цен по (provider, model, valid_from): строка с тем же
 *     ключом заменяется на месте (у модели (provider_id, valid_from) уникальна
 *     — «замещённая» строка — это та же строка с новыми условиями);
 *  2) все rated-события, тарифицированные по заменённым строкам,
 *     пересчитываются (priceRow события не меняется — меняется стоимость по
 *     новым условиям той же строки); ratedAt события не изменяется;
 *  3) журнал: одна запись RepriceRun + по одной RepriceEventCorrection на
 *     каждое пересчитанное событие (снимок условий «до»/«после», чтобы
 *     аудит был самодостаточен);
 *  4) итог: REPRICED_EVENTS=<n>, REPRICE_DELTA_USD=<новая сумма − старая>
 *     (6 знаков). Повторный запуск той же коррекции пересчитывает те же
 *     события без изменения суммы (дельта 0.000000).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricingCalculator $calculator = new PricingCalculator(),
    ) {
    }

    /** @throws BadInputException при некорректном файле */
    public function apply(string $path): RepriceSummary
    {
        $data = JsonFile::readObject($path);

        /** @var list<array<string, mixed>> $rawEntries */
        $rawEntries = JsonFile::listSection($data, 'prices');
        if ($rawEntries === []) {
            throw new BadInputException('prices: ожидается непустой список строк цен');
        }

        // Секции providers/models (если есть) здесь не применяются: коррекция
        // действует только на уже известные строки цен.
        $entries = $this->validateEntries($rawEntries);

        return $this->em->getConnection()->transactional(function () use ($path, $entries): RepriceSummary {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

            // 1) Sнимок старых условий + upsert строк цен по key
            //    (provider/model@valid_from); последняя запись файла
            //    с тем же ключом выигрывает. Сущности в рамках этого
            //    запуска отслеживаем в $byKey — повторный findOneBy
            //    не видит ещё не-flushнутых записей.
            $oldTerms = [];
            $byKey = [];
            foreach ($entries as $entry) {
                $key = $this->entryKey($entry);

                if (!array_key_exists($key, $byKey)) {
                    $model = $this->em
                        ->getRepository(Model::class)
                        ->findOneBy(['provider' => $entry['provider'], 'code' => $entry['model']]);
                    if ($model === null) {
                        throw new BadInputException(
                            "prices: модель «{$entry['provider']->getCode()}/{$entry['model']}» не найдена в базе",
                        );
                    }

                    $existing = $this->em
                        ->getRepository(PriceRow::class)
                        ->findOneBy(['model' => $model, 'validFrom' => $entry['validFrom']]);
                    if ($existing !== null) {
                        $oldTerms[$key] = ['unit' => $existing->getUnit(), 'rates' => $existing->getRates()];
                        $byKey[$key] = $existing;
                    } else {
                        $existing = new PriceRow($model, $entry['validFrom'], $entry['currency'], $entry['unit'], $entry['rates']);
                        $this->em->persist($existing);
                        $byKey[$key] = $existing;
                    }
                }

                $byKey[$key]->setTerms($entry['currency'], $entry['unit'], $entry['rates']);
            }
            $this->em->flush(); // id у новых строк

            /** @var list<PriceRow> $rows */
            $rows = array_values(array_unique(array_values($byKey), SORT_REGULAR));
            $rowIds = array_map(static fn (PriceRow $r): ?int => $r->getId(), $rows);
            $rowIds = array_values(array_unique(array_filter($rowIds, static fn (?int $id): bool => $id !== null)));

            // 2) Rated-события, тарифицированные по заменённым строкам.
            /** @var UsageEvent[] $events */
            $events = array_values($this->em
                ->getRepository(UsageEvent::class)
                ->findBy(['status' => UsageEvent::STATUS_RATED, 'priceRow' => $rowIds]));

            // 3) Пересчёт и журнал.
            $n = 0;
            $delta = '0';
            $corrections = [];
            foreach ($events as $event) {
                $row = $event->getPriceRow();
                if ($row === null) {
                    continue; // rated-событие без строки — аномалия, пропускаем
                }

                $oldCost = (string) $event->getCostUsd();
                $provider = $event->getProvider();
                $offPeak = OffPeakPricing::multiplierAt(
                    $provider->getOffPeakWindowStart(),
                    $provider->getOffPeakWindowEnd(),
                    $provider->getOffPeakMultiplier(),
                    $this->minutesOfDay($event->getTs()),
                );
                $newCost = $this->calculator->calculateEventCost(
                    $row->unitAsEnum(),
                    $event->tokens(),
                    $row->getRates(),
                    $offPeak,
                );

                $event->reprice($row, $newCost);
                ++$n;
                $delta = bcadd($delta, bcsub($newCost, $oldCost, Decimal::SCALE), Decimal::SCALE);

                $terms = $oldTerms[$row->key()] ?? ['unit' => $row->getUnit(), 'rates' => $row->getRates()];
                $corrections[] = [
                    'event' => $event,
                    'row' => $row,
                    'oldUnit' => $terms['unit'],
                    'oldRates' => $terms['rates'],
                    'newUnit' => $row->getUnit(),
                    'newRates' => $row->getRates(),
                    'oldCost' => $oldCost,
                    'newCost' => $newCost,
                ];
            }

            if ($n > 0) {
                $run = new RepriceRun(
                    sourceFile: basename($path),
                    affectedPriceKeys: array_map(static fn (PriceRow $r): string => $r->key(), $rows),
                    eventsRepriced: $n,
                    deltaUsd: Decimal::money6($delta),
                    appliedAt: $now,
                );
                $this->em->persist($run);
                foreach ($corrections as $c) {
                    $this->em->persist(new RepriceEventCorrection(
                        run: $run,
                        event: $c['event'],
                        oldPriceRow: $c['row'],
                        newPriceRow: $c['row'],
                        oldUnit: $c['oldUnit'],
                        oldRates: $c['oldRates'],
                        newRates: $c['newRates'],
                        oldCostUsd: $c['oldCost'],
                        newCostUsd: $c['newCost'],
                    ));
                }
            }

            $this->em->flush();

            return new RepriceSummary($n, Decimal::money6($delta));
        });
    }

    /**
     * Проверка строк коррекции ДО любой записи: провайдер/модель обязаны
     * существовать в базе; остальное — как при импорте цен.
     *
     * @param list<array<string, mixed>> $rawEntries
     *
     * @return list<array{provider: Provider, model: string, validFrom: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>}>
     */
    private function validateEntries(array $rawEntries): array
    {
        $entries = [];
        foreach ($rawEntries as $index => $raw) {
            $context = "prices[{$index}]";
            $entry = JsonFile::item($rawEntries, $index, 'prices');

            $providerCode = SpecReader::requiredString($entry, 'provider', $context, 128);
            $provider = $this->em
                ->getRepository(Provider::class)
                ->findOneBy(['code' => $providerCode]);
            if ($provider === null) {
                throw new BadInputException("{$context}.provider: провайдер «{$providerCode}» не найден в базе");
            }

            $modelCode = SpecReader::requiredString($entry, 'model', $context, 128);
            $model = $this->em
                ->getRepository(Model::class)
                ->findOneBy(['provider' => $provider, 'code' => $modelCode]);
            if ($model === null) {
                throw new BadInputException("{$context}: модель «{$providerCode}/{$modelCode}» не найдена в базе");
            }

            $unit = PriceUnit::fromWire(SpecReader::requiredString($entry, 'unit', $context, 40));
            $rates = SpecReader::rates($entry, $unit, $context);

            $entries[] = [
                'provider' => $provider,
                'model' => $modelCode,
                'validFrom' => StrictIsoUtc::parse(SpecReader::requiredString($entry, 'valid_from', $context, 40)),
                'currency' => SpecReader::requiredString($entry, 'currency', $context, 16),
                'unit' => $unit->value,
                'rates' => $rates,
            ];
        }

        return $entries;
    }

    /**
     * @param array{provider: Provider, model: string, validFrom: \DateTimeImmutable} $entry
     */
    private function entryKey(array $entry): string
    {
        return $entry['provider']->getCode().'/'.$entry['model'].'@'
            .$entry['validFrom']->format('Y-m-d\TH:i:s\Z');
    }

    private function minutesOfDay(\DateTimeImmutable $dt): int
    {
        return (int) $dt->format('G') * 60 + (int) $dt->format('i');
    }
}
