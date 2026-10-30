<?php

declare(strict_types=1);

namespace App\Pricing\Application;

use App\Pricing\Domain\LlmModel;
use App\Pricing\Domain\PriceRow;
use App\Pricing\Domain\Provider;
use App\Pricing\Infrastructure\PricingRepository;
use App\Shared\InvalidInput;
use App\Usage\Domain\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника цен.
 */
final class PriceImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricingRepository $repo,
    ) {
    }

    /**
     * Импорт справочника. Изменение сумм строки, которой уже тарифицированы события,
     * запрещено — для этого есть app:billing:reprice.
     *
     * @return array{providers: int, models: int, prices: int}
     */
    public function import(PriceList $list): array
    {
        return $this->em->wrapInTransaction(function () use ($list): array {
            $providers = [];
            foreach ($list->providers as $p) {
                $provider = $this->repo->provider($p['code']);
                if ($provider === null) {
                    $provider = new Provider($p['code'], $p['name'], $p['offPeak']);
                    $this->em->persist($provider);
                } else {
                    $provider->update($p['name'], $p['offPeak']);
                }
                $providers[$p['code']] = $provider;
            }
            $this->em->flush();

            $models = [];
            foreach ($list->models as $m) {
                $model = $this->repo->model($m['provider'], $m['code']);
                if ($model === null) {
                    $provider = $providers[$m['provider']] ?? $this->repo->provider($m['provider'])
                        ?? throw new InvalidInput(sprintf('Модель %s/%s: неизвестный провайдер', $m['provider'], $m['code']));
                    $model = new LlmModel($provider, $m['code']);
                    $this->em->persist($model);
                }
                $models[$m['provider'].'/'.$m['code']] = $model;
            }
            $this->em->flush();

            $this->upsertPrices($list, $models, allowChangeOfUsedRows: false);

            return ['providers' => count($list->providers), 'models' => count($list->models), 'prices' => count($list->prices)];
        });
    }

    /**
     * @param array<string, LlmModel> $models
     * @return list<array{row: PriceRow, created: bool}> строки, которые были созданы или изменены
     */
    public function upsertPrices(PriceList $list, array $models, bool $allowChangeOfUsedRows): array
    {
        $changed = [];
        foreach ($list->prices as $r) {
            $key = $r['provider'].'/'.$r['model'];
            $model = $models[$key] ?? $this->repo->model($r['provider'], $r['model'])
                ?? throw new InvalidInput(sprintf('Цена для неизвестной модели %s', $key));
            $models[$key] = $model;

            $row = $this->repo->priceRow($model, $r['validFrom']);
            if ($row === null) {
                $row = new PriceRow($model, $r['validFrom'], $r['currency'], $r['rates']);
                $this->em->persist($row);
                $changed[] = ['row' => $row, 'created' => true];
                continue;
            }
            if ($row->rates()->equals($r['rates'])) {
                continue;
            }
            if (!$allowChangeOfUsedRows && $this->isUsed($row)) {
                throw new InvalidInput(sprintf(
                    'Цена %s с %s уже применена к рассчитанным событиям; для исправления используйте app:billing:reprice',
                    $key,
                    $r['validFrom']->format(DATE_ATOM),
                ));
            }
            $row->change($r['currency'], $r['rates']);
            $changed[] = ['row' => $row, 'created' => false];
        }
        $this->em->flush();

        return $changed;
    }

    private function isUsed(PriceRow $row): bool
    {
        return $this->em->getRepository(UsageEvent::class)->count(['priceRow' => $row]) > 0;
    }
}
