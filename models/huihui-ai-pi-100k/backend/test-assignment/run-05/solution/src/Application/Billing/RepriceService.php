<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Exception\InputException;
use App\Application\Import\CatalogUpsert;
use App\Application\Import\PriceFileParser;
use App\Application\Import\PriceRecord;
use App\Application\Import\ParsedPrices;
use App\Domain\Billing\EventPricer;
use App\Domain\Billing\OffPeak;
use App\Domain\Billing\TokenUsage;
use App\Domain\Money\MoneyMath;
use App\Infrastructure\Persistence\Entity\Model;
use App\Infrastructure\Persistence\Entity\PriceRow;
use App\Infrastructure\Persistence\Entity\Provider;
use App\Infrastructure\Persistence\Entity\UsageEvent;
use App\Infrastructure\Persistence\PriceRowMapper;
use App\Infrastructure\Persistence\Repository\PriceRowRepository;
use App\Infrastructure\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус, app:billing:reprice).
 *
 * Механика:
 *  1. Строки ценового файла upsert-ятся по (provider, model, validFrom)
 *     в уже загруженный справочник: провайдер и модель обязаны
 *     существовать в БД (секции providers/models в файле коррекции
 *     опциональны и не используются).
 *  2. «Заменённой» считается строка, чей природный ключ уже был в БД
 *     до применения файла.
 *  3. Пересчитываются события, тарифицированные заменёнными строками
 *     (метка priceValidFrom фиксирует, какой именно справочник лег
 *     в основу стоимости события): по актуальным строкам цен выбирается
 *     новая применимая строка (наибольший validFrom <= ts); если её нет —
 *     событие возвращается в unrated.
 *  4. Dельта — новая сумма за минусом старой по этим событиям,
 *     ровно 6 знаков, бцшмм.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceFileParser $parser,
        private readonly CatalogUpsert $upsert,
        private readonly EventPricer $pricer,
    ) {
    }

    /**
     * @throws InputException
     */
    public function reprice(string $path): RepriceResult
    {
        $file = $this->parser->parse($path);

        if (count($file->prices) === 0) {
            throw new InputException('В файле коррекции нет строк цен ("prices" пустой)');
        }

        $this->assertCatalog($file);

        // Ключи, которые уже существовали ДО применения файла:
        // именно события, рассчитанные по ним, подлежат пересчёту.
        $replaced = [];
        foreach ($file->prices as $record) {
            if (
                $this->em->getRepository(PriceRow::class)->findByNaturalKey(
                    $record->provider,
                    $record->model,
                    $record->validFrom,
                ) !== null
            ) {
                $replaced[] = $record;
            }
        }

        $em = $this->em;

        // Шаг 1: применить исправленные строки (idempotent upsert).
        $em->getConnection()->transactional(function () use ($file, $em): void {
            foreach ($file->prices as $record) {
                $this->upsert->upsertPriceRow($record);
            }
            $em->flush();
        });

        // Шаг 2: пересчёт затронутых событий.
        return $this->recalculate($replaced);
    }

    private function assertCatalog(ParsedPrices $file): void
    {
        $providerRepo = $this->em->getRepository(Provider::class);
        $modelRepo = $this->em->getRepository(Model::class);

        foreach ($file->prices as $i => $record) {
            $label = sprintf('prices[%d]', $i);

            if ($providerRepo->findOneBy(['code' => $record->provider]) === null) {
                throw new InputException(sprintf('%s: провайдер "%s" отсутствует в базе', $label, $record->provider));
            }

            if ($modelRepo->findOneBy(['provider' => $record->provider, 'code' => $record->model]) === null) {
                throw new InputException(sprintf(
                    '%s: модель "%s/%s" отсутствует в базе',
                    $label, $record->provider, $record->model,
                ));
            }
        }
    }

    /**
     * @param PriceRecord[] $replaced
     */
    private function recalculate(array $replaced): RepriceResult
    {
        $em = $this->em;
        $count = 0;
        $delta = '0';
        /** @var array<string, OffPeak|null> $offPeakCache */
        $offPeakCache = [];

        $this->em->getConnection()->transactional(function () use ($replaced, &$count, &$delta, &$offPeakCache): void {
            $eventRepo = $this->em->getRepository(UsageEvent::class);
            $priceRepo = $this->em->getRepository(PriceRow::class);
            $providerRepo = $this->em->getRepository(Provider::class);

            foreach ($replaced as $key) {
                /** @var UsageEvent[] $events */
                $events = $eventRepo->findRatedByPriceRow($key->provider, $key->model, $key->validFrom);

                foreach ($events as $event) {
                    $oldCost = (string) $event->cost;

                    $newRow = $priceRepo->latestAtOrBefore($event->provider, $event->model, $event->ts);

                    if ($newRow !== null) {
                        if (!array_key_exists($event->provider, $offPeakCache)) {
                            $provider = $providerRepo->findOneBy(['code' => $event->provider]);
                            $offPeakCache[$event->provider] = $provider === null
                                ? null
                            : OffPeak::fromWindow(
                                $provider->offPeakStart,
                                $provider->offPeakEnd,
                                $provider->offPeakMultiplier,
                            );
                        }

                        $event->status = UsageEvent::STATUS_RATED;
                        $event->cost = $this->pricer->price(
                            PriceRowMapper::toSpec($newRow),
                            TokenUsage::create(
                                $event->tokensInput,
                                $event->tokensOutput,
                                $event->tokensCacheRead,
                                $event->tokensCacheWrite,
                            ),
                            $offPeakCache[$event->provider],
                            $event->ts,
                        );
                        $event->priceValidFrom = $newRow->validFrom;
                        $newCost = (string) $event->cost;
                    } else {
                        $event->status = UsageEvent::STATUS_UNRATED;
                        $event->cost = null;
                        $event->priceValidFrom = null;
                        $newCost = '0';
                    }

                    $count++;
                    // дельта по событию = новая стоимость − старая (обе — ровно 6 знаков)
                    $delta = bcadd($delta, bcsub($newCost, $oldCost, 6), 6);
                }
            }

            $this->em->flush();
        });

        return new RepriceResult(
            repricedEvents: $count,
            deltaUsd: MoneyMath::normalize6($delta),
        );
    }
}
