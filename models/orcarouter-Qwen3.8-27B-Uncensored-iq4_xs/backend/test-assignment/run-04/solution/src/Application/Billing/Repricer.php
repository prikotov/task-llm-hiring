<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Catalog\CatalogInput;
use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Usage\EventPricingCalculator;
use App\Exception\DataImportException;
use App\Domain\Money\Money;
use App\Infrastructure\Persistence\PriceRowRepository;
use App\Infrastructure\Persistence\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены.
 *
 * 1) Присланные строки цен апситемся по (provider, model, valid_from)
 *    по данным уже загруженного справочника: провайдер и модель должны
 *    существовать в базе (секции providers/models в файле коррекции
 *    могут отсутствовать или быть пустыми).
 * 2) Пересчитываются рассчитанные события затронутых моделей: событие,
 *    чья "текущая" действующая строка изменилась (строка была заменена по
 *    содержимому или появилась новая строка цены, ставшая действующей на
 *    момент ts), вычисляется заново — та же формула: действующая цена,
 *    оф-пик, единственное округление HALF_UP до 6 знаков.
 *    Механизм — перезапись стоимости события (идемпотентно; без доначислений
 *    и задвоений в отчёте).
 */
final class Repricer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceRowRepository $priceRows,
        private readonly UsageEventRepository $events,
        private readonly EventPricingCalculator $calculator,
    ) {
    }

    public function reprice(string $file): RepriceResult
    {
        $data = CatalogInput::readJson($file);
        $prices = CatalogInput::parsePrices($data, $file);

        $result = $this->em->wrapInTransaction(function () use ($prices): RepriceResult {
            $replacedRows = [];
            $modelIds = [];
            $providers = [];
            $rowsByKey = [];

            foreach ($prices as $price) {
                $model = $this->resolveModel($price['provider'], $price['model']);
                $key = $model->providerCode().'|'.$model->code().'|'.$price['validFrom']->getTimestamp();
                $row = $rowsByKey[$key]
                    ?? $this->priceRows->findOneByModelAndValidFrom(
                        $model->providerCode(),
                        $model->code(),
                        $price['validFrom'],
                    )
                    ?? new PriceRow(
                        $model,
                        $price['validFrom'],
                        $price['currency'],
                        $price['unit'],
                        $price['rates'],
                    );
                $row->apply($price['currency'], $price['unit'], $price['rates']);
                $this->em->persist($row);

                $rowsByKey[$key] = $row;
                $replacedRows[] = $row;
                $modelIds[$model->id()] = true;
            }

            $this->em->flush(); // зафиксировать id новых строк до пересчёта
            $replacedRowIds = [];
            foreach ($replacedRows as $row) {
                $replacedRowIds[$row->id() ?? -1] = true;
            }

            $count = 0;
            $delta = Money::zero();

            foreach ($this->events->findRatedForModelIds(array_keys($modelIds)) as $event) {
                $provider = $providers[$event->provider()] ??= $this->em->find(Provider::class, $event->provider());

                $newPrice = $this->priceRows->findEffectiveForModel(
                    $event->provider(),
                    $event->model()->code(),
                    $event->ts(),
                );
                if ($newPrice === null) {
                    continue; // теоретически недостижимо: заменённая строка сама покрывает ts
                }

                $newCost = $this->calculator->computeEventCost($event, $newPrice, $provider?->offPeak());

                $oldCost = $event->money();
                // Пересчёт нужен, только если изменилась действующая строка
                // или изменилась итоговая стоимость (повторная коррекция
                // без изменений — событие не трогаем).
                $touched = $newPrice->id() !== $event->priceRow()?->id()
                    || $newCost->equals($oldCost) === false;

                if ($touched === false) {
                    continue;
                }

                $event->repriceWith($newPrice, $newCost);
                $delta = $delta->add($newCost->subtract($oldCost));
                $count++;
            }

            return new RepriceResult($count, $delta);
        });

        return $result;
    }

    private function resolveModel(string $providerCode, string $code): Model
    {
        $model = $this->em->getRepository(Model::class)->findOneBy([
            'providerCode' => $providerCode,
            'code' => $code,
        ]);
        if ($model === null) {
            throw new DataImportException(sprintf(
                'Model "%s/%s" is not found in the reference data; it must be imported before reprice.',
                $providerCode,
                $code,
            ));
        }

        return $model;
    }
}
