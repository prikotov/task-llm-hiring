<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Result\RepriceResult;
use App\Domain\Billing\Money;
use App\Domain\Billing\PriceSelection;
use App\Domain\Billing\PricingCalculator;
use App\Domain\Exception\BillingException;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус, app:billing:reprice).
 *
 * Провайдер присылает исправленную строку цен (файл того же формата,
 * но секции providers/models могут отсутствовать — опорой служит
 * уже загруженный справочник: провайдер и модель должны существовать в базе).
 *
 * Механизм: перезапись значения строки (upsert по (provider, model, valid_from))
 * + пересчёт тех рассчитанных событий, которые тарифицировались именно этой
 * строкой (опорный факт — price_id на событии). Старая стоимость события
 * заменяется новой «на месте», поэтому app:billing:report после reprice
 * показывает пересчитанные суммы без задвоения и корректировочных записей.
 *
 * Результат:
 *  - REPRICED_EVENTS=<n> — число пересчитанных событий;
 *  - REPRICE_DELTA_USD=<x.xxxxxx> — новая сумма их стоимостей минус старая (6 знаков).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
        private readonly UsageEventRepository $events,
        private readonly PricesFileParser $parser,
        private readonly PricingCalculator $calculator,
    ) {
    }

    public function reprice(string $file): RepriceResult
    {
        $data = $this->parser->parseFile($file);

        return $this->em->wrapInTransaction(function () use ($data): RepriceResult {
            // Секции providers/models файла коррекции игнорируются:
            // строки валидируются по справочнику БД (см. класс).

            $providers = [];
            foreach ($this->providers->findAll() as $provider) {
                $providers[$provider->code()] = $provider;
            }

            $models = [];
            foreach ($this->models->findAll() as $model) {
                $models[$model->provider()->code()."\0".$model->code()] = $model;
            }

            $replacedPriceIds = []; // id строк цен, значения которых заменены

            foreach ($data->prices as $entry) {
                $provider = $providers[$entry->provider] ?? null;
                if ($provider === null) {
                    throw new BillingException(sprintf(
                        'Провайдер "%s" (строка цен %s/%s) отсутствует в базе — сначала загрузите справочник',
                        $entry->provider,
                        $entry->provider,
                        $entry->model,
                    ));
                }

                $model = $models[$entry->provider."\0".$entry->model] ?? null;
                if ($model === null) {
                    throw new BillingException(sprintf(
                        'Модель "%s/%s" отсутствует в базе — сначала загрузите справочник',
                        $entry->provider,
                        $entry->model,
                    ));
                }

                $price = $this->prices->findByModelAndValidFrom($model, $entry->validFrom);
                if ($price === null) {
                    // Новая строка (такого valid_from не было): она ничего не
                    // «заменяет», рассчитанных по ней событий быть не может.
                    $price = new Price($model, $entry->validFrom, $entry->currency, $entry->unit, $entry->rates);
                    $this->em->persist($price);
                } else {
                    $replacedPriceIds[] = (int) $price->id();
                    $price->update($entry->currency, $entry->unit, $entry->rates);
                }
            }

            $this->em->flush();

            $affected = $this->events->findRatedByPriceIdIn($replacedPriceIds);

            $oldSum = '0';
            $newSum = '0';

            foreach ($affected as $event) {
                $oldSum = Money::add($oldSum, (string) $event->cost());

                // Повторный выбор цены по актуальному справочнику:
                // обычно это та же строка (её valid_from не меняется)
                // с новыми значениями ставок.
                $model = $this->models->findByProviderCodeAndCode($event->providerCode(), $event->modelCode());
                $best = $model === null
                    ? null
                    : PriceSelection::best($event->ts(), $this->prices->findByModel($model));

                if ($best === null) {
                    // Фактически недостижимо (upsert строки не удаляет её),
                    // но на всякий случай: событие теряет стоимость
                    // и становится unrated; его старая стоимость
                    // остаётся в oldSum, новой нет.
                    $event->clearRating();
                    continue;
                }

                $cost = $this->calculator->price(
                    $best,
                    $event->tokenUsage(),
                    ($providers[$event->providerCode()] ?? null)?->offPeakPolicy(),
                    $event->ts(),
                );

                $event->applyRating($cost, (int) $best->id());
                $newSum = Money::add($newSum, $cost);
            }

            $this->em->flush();

            return new RepriceResult(
                events: \count($affected),
                deltaUsd: Money::format(Money::sub($newSum, $oldSum)),
            );
        });
    }
}
