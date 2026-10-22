<?php

declare(strict_types=1);

namespace App\Importer;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт справочника (провайдеры, модели, строки цен).
 *
 * Идемпотентный upsert:
 *  - провайдеры по code;
 *  - модели по (provider, code);
 *  - строки цен по (provider, model, valid_from).
 * Повторная загрузка тех же сумм данных не изменяет.
 */
final class PriceImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ImportException
     */
    public function importFile(string $path): void
    {
        $file = PriceFile::parse($path, requirePrices: true);

        $this->em->wrapInTransaction(function () use ($file): void {
            $providers = $this->upsertProviders($file->providers);
            $models = $this->upsertModels($file->models, $providers);
            foreach ($file->prices as $spec) {
                $this->upsertPrice($spec, $providers, $models);
            }
        });
    }

    /**
     * @param array<string, array{name: string, offPeak: ?\App\Domain\ValueObject\OffPeak}> $specProviders
     *
     * @return array<string, Provider> code => сущность
     *
     * @throws ImportException
     */
    private function upsertProviders(array $specProviders): array
    {
        /** @var array<string, Provider> $existing */
        $existing = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $p) {
            $existing[$p->code()] = $p;
        }

        $result = $existing;
        foreach ($specProviders as $code => $spec) {
            $provider = $existing["$code"] ?? new Provider();
            if (!isset($existing["$code"])) {
                $provider->setCode($code);
                $this->em->persist($provider);
            }
            $provider->setName($spec['name']);
            $provider->setOffPeak($spec['offPeak']);
            $result["$code"] = $provider;
        }

        return $result;
    }

    /**
     * @param array<string, bool> $specModels
     * @param array<string, Provider> $providers
     *
     * @return array<string, Model> "provider|code" => сущность
     *
     * @throws ImportException
     */
    private function upsertModels(array $specModels, array $providers): array
    {
        /** @var array<string, Model> $existing */
        $existing = [];
        foreach ($this->em->getRepository(Model::class)->findAll() as $m) {
            $existing[$m->provider().'|'.$m->code()] = $m;
        }

        $result = $existing;
        foreach ($specModels as $key => $_) {
            [$providerCode, $code] = explode('|', $key);
            if (!isset($providers["$providerCode"])) {
                throw new ImportException(sprintf('models: модель "%s" ссылается на неизвестного провайдера "%s"', $key, $providerCode));
            }
            $model = $existing["$key"] ?? new Model();
            if (!isset($existing["$key"])) {
                $this->em->persist($model);
            }
            $model->setProvider($providerCode);
            $model->setCode($code);
            $result["$key"] = $model;
        }

        return $result;
    }

    /**
     * @param array{name: string, offPeak: ?\App\Domain\ValueObject\OffPeak}|Provider $providers
     * @param array<string, Model> $models
     *
     * @throws ImportException
     */
    private function upsertPrice(array $spec, array $providers, array $models): void
    {
        $providerCode = $spec['provider'];
        $modelCode = $spec['model'];

        if (!array_key_exists($providerCode, $providers)) {
            throw new ImportException(sprintf('prices: провайдер "%s" не найден в файле или справочнике', $providerCode));
        }
        $key = $providerCode.'|'.$modelCode;
        if (!isset($models["$key"])) {
            throw new ImportException(sprintf('prices: модель "%s" не найдена в файле или справочнике', $key));
        }

        $repository = $this->em->getRepository(PriceRow::class);
        $row = $repository->findOneBy([
            'provider' => $providerCode,
            'model' => $modelCode,
            'validFrom' => $spec['validFrom'],
        ]);

        $row ??= new PriceRow();
        if ($row->id() === null) {
            $row->setProvider($providerCode);
            $row->setModel($modelCode);
            $row->setValidFrom($spec['validFrom']);
            $this->em->persist($row);
        }
        $row->set($spec['currency'], $spec['unit'], $spec['rates']);
    }
}
