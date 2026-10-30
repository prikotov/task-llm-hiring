<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Parsers\PricesFile;
use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника:
 *   провайдеры — по code,
 *   модели — по (provider, code),
 *   строки цен — по (provider, model, valid_from).
 *
 * Сначала полностью разбирается и валидируется файл, затем пишется БД —
 * при ошибке валидации изменения не фиксируются.
 */
final class CatalogImporter
{
    use UsesTransactions;
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * Упорядоченная пара: сначала провайдеры/модели, затем цены.
     */
    public function import(string $json, bool $requirePrices = true): void
    {
        $data = PricesFile::parse($json, $requirePrices);

        $this->inTransaction(function () use ($data): void {
            foreach ($data['providers'] as $p) {
                $provider = $this->em->getRepository(Provider::class)
                    ->findOneBy(['code' => $p['code']]);
                if ($provider === null) {
                    $provider = new Provider();
                    $provider->code = $p['code'];
                    $this->em->persist($provider);
                }

                $provider->name = $p['name'];

                if ($p['offpeak'] === null) {
                    $provider->offPeakWindowStart = null;
                    $provider->offPeakWindowEnd = null;
                    $provider->offPeakMultiplier = null;
                } else {
                    $provider->offPeakWindowStart = $p['offpeak']['start'];
                    $provider->offPeakWindowEnd = $p['offpeak']['end'];
                    $provider->offPeakMultiplier = $p['offpeak']['multiplier'];
                }
            }

            foreach ($data['models'] as $m) {
                $existing = $this->em->getRepository(Model::class)
                    ->findOneBy(['provider' => $m['provider'], 'code' => $m['code']]);
                if ($existing === null) {
                    $model = new Model();
                    $model->provider = $m['provider'];
                    $model->code = $m['code'];
                    $this->em->persist($model);
                } elseif ($this->em->getRepository(Provider::class)->find($m['provider']) === null) {
                    throw ImportException::invalid('models', sprintf('провайдер "%s" не найден в справочнике', $m['provider']));
                }
            }

            // фиксация провайдеров/моделей: цены ссылаются на id модели
            $this->em->flush();

            // цены: провайдер и модель обязаны существовать в базе
            foreach ($data['prices'] as $price) {
                if ($this->em->getRepository(Provider::class)->find($price['provider']) === null) {
                    throw ImportException::invalid('prices', sprintf('провайдер "%s" не найден в справочнике', $price['provider']));
                }

                $model = $this->em->getRepository(Model::class)
                    ->findOneBy(['provider' => $price['provider'], 'code' => $price['model']]);
                if ($model === null) {
                    throw ImportException::invalid('prices', sprintf('модель "%s/%s" не найдена в справочнике', $price['provider'], $price['model']));
                }

                $row = $this->priceRowByModel($model, $price['valid_from']);
                if ($row === null) {
                    $row = new PriceRow();
                    $row->model = $model->id;
                    $row->validFrom = $price['valid_from'];
                    $this->em->persist($row);
                }

                $row->currency = $price['currency'];
                $row->unit = $price['unit'];
                $row->rates = json_encode($price['rates'], JSON_THROW_ON_ERROR);
            }
        });
    }

    public function priceRowByModel(Model $model, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->em->getRepository(PriceRow::class)->findOneBy([
            'model' => $model->id,
            'validFrom' => $validFrom,
        ]);
    }

    /** Строка цен по (provider, model, valid_from); null, если её нет. */
    public function findPriceRow(string $provider, string $modelCode, \DateTimeImmutable $validFrom): ?PriceRow
    {
        $model = $this->em->getRepository(Model::class)->findOneBy([
            'provider' => $provider,
            'code' => $modelCode,
        ]);
        if ($model === null) {
            return null;
        }

        return $this->em->getRepository(PriceRow::class)->findOneBy([
            'model' => $model->id,
            'validFrom' => $validFrom,
        ]);
    }
}
