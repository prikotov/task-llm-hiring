<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка файла истории цен:
 *  - провайдеры — upsert по code;
 *  - модели — upsert по (provider, code);
 *  - строки цен — upsert по (provider, model, valid_from).
 * Повторная загрузка тех же данных не изменяет содержимое БД.
 */
final class PriceImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(PriceFile $file): void
    {
        $this->em->getConnection()->transactional(
            fn (): mixed => $this->doImport($file),
        );
    }

    private function doImport(PriceFile $file): void
    {
        /** @var array<string, Provider> $providers по code */
        $providers = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $p) {
            $providers[$p->getCode()] = $p;
        }

        foreach ($file->getProviders() as $spec) {
            $provider = $providers[$spec->getCode()] ?? new Provider($spec->getCode(), $spec->getName());
            $provider->setName($spec->getName());

            $offPeak = $spec->getOffPeak();
            $provider->replaceOffPeak(
                $offPeak['windowStart'] ?? null,
                $offPeak['windowEnd'] ?? null,
                $offPeak['multiplier'] ?? null,
            );

            $this->em->persist($provider);
            $providers[$spec->getCode()] = $provider;
        }
        $this->em->flush(); // нужны id для FK

        /** @var array<string, Model> $models по "provider|code" */
        $models = [];
        foreach ($this->em->getRepository(Model::class)->findAll() as $m) {
            $provider = $providers[$m->getProviderId()] ?? $this->em->find(Provider::class, $m->getProviderId());
            $models[$provider->getCode().'|'.$m->getCode()] = $m;
        }

        foreach ($file->getModels() as $spec) {
            $key = $spec->getProviderCode().'|'.$spec->getCode();
            $model = $models[$key]
                ?? new Model($providers[$spec->getProviderCode()]->getId(), $spec->getCode());
            $this->em->persist($model);
            $models[$key] = $model;
        }
        $this->em->flush();

        /** @var array<string, PriceRow> $rows по "modelId|unix(validFrom)" */
        $rows = [];
        foreach ($this->em->getRepository(PriceRow::class)->findAll() as $row) {
            $rows[$row->getModelId().'|'.$row->getValidFrom()->format('U')] = $row;
        }

        foreach ($file->getPrices() as $spec) {
            $model = $models[$spec->getProviderCode().'|'.$spec->getModel()];
            $key = $model->getId().'|'.$spec->getValidFrom()->format('U');

            $row = $rows[$key] ?? new PriceRow(
                $model->getId(),
                $spec->getValidFrom(),
                $spec->getCurrency(),
                $spec->getUnit(),
                $spec->getRates(),
            );

            $stored = [
                'unit' => $row->getUnit(),
                'currency' => $row->getCurrency(),
                'rates' => $row->getRates(),
            ];
            if (!$spec->sameContent($stored)) {
                $row->replaceRates($spec->getCurrency(), $spec->getUnit(), $spec->getRates());
            }

            $this->em->persist($row);
            $rows[$key] = $row;
        }
        $this->em->flush();
    }
}
