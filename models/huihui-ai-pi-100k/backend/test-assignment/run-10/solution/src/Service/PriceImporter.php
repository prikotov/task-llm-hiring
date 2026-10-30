<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ModelData;
use App\Dto\PriceData;
use App\Dto\ProviderData;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Exception\InputError;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника цен в отдельной транзакции.
 *
 * Ключи:
 *   providers   — по code
 *   models      — по (provider, code)
 *   price_rows  — по (provider, model, valid_from)
 */
final class PriceImporter
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param array{providers: ProviderData[], models: ModelData[], prices: PriceData[]} $parsed
     */
    public function import(array $parsed): void
    {
        $this->em->wrapInTransaction(function () use ($parsed): void {
            $providerMap = [];
            foreach ($parsed['providers'] as $data) {
                $providerMap[$data->code] = $this->upsertProvider($data);
            }

            $modelMap = [];
            foreach ($parsed['models'] as $data) {
                $modelMap[$data->provider . "\x00" . $data->code]
                    = $this->upsertModel($data, $providerMap);
            }

            foreach ($parsed['prices'] as $data) {
                $this->upsertPriceRow($data, $providerMap, $modelMap);
            }
        });
    }

    private function upsertProvider(ProviderData $data): Provider
    {
        $existing = $this->em->getRepository(Provider::class)->findByCode($data->code);

        if ($existing !== null) {
            $existing->setName($data->name);
            $this->applyOffPeak($existing, $data->offPeak);

            return $existing;
        }

        $provider = new Provider($data->code, $data->name);
        $this->applyOffPeak($provider, $data->offPeak);
        $this->em->persist($provider);

        return $provider;
    }

    private function applyOffPeak(Provider $provider, ?array $offPeak): void
    {
        if ($offPeak === null) {
            $provider->setOffPeak(null, null, null);

            return;
        }

        $provider->setOffPeak($offPeak['window_start'], $offPeak['window_end'], $offPeak['multiplier']);
    }

    /**
     * @param array<string, Provider> $providerMap
     */
    private function upsertModel(ModelData $data, array $providerMap): Model
    {
        $provider = $providerMap[$data->provider]
            ?? $this->em->getRepository(Provider::class)->findByCode($data->provider);

        if ($provider === null) {
            throw InputError::because(string: 'model ' . $data->provider . '/' . $data->code
                . ': провайдер "' . $data->provider . '" не найден в справочнике');
        }

        $existing = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $data->code]);

        return $existing ?? $this->persistModel(new Model($provider, $data->code));
    }

    private function persistModel(Model $model): Model
    {
        $this->em->persist($model);

        return $model;
    }

    /**
     * @param array<string, Provider> $providerMap
     * @param array<string, Model> $modelMap
     */
    private function upsertPriceRow(PriceData $data, array $providerMap, array $modelMap): void
    {
        $provider = $providerMap[$data->provider]
            ?? $this->em->getRepository(Provider::class)->findByCode($data->provider);

        if ($provider === null) {
            throw InputError::because(string: 'price row ' . $data->provider . '/' . $data->model
                . ': провайдер "' . $data->provider . '" не найден в справочнике');
        }

        $key = $data->provider . "\x00" . $data->model;
        $model = $modelMap[$key]
            ?? $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $data->model]);

        if ($model === null) {
            throw InputError::because(string: 'price row ' . $data->provider . '/' . $data->model
                . ': модель "' . $data->model . '" у провайдера "' . $data->provider . '" не найдена в справочнике');
        }

        $existing = $this->em->getRepository(PriceRow::class)
            ->findOneBy(['model' => $model, 'validFrom' => $data->validFrom]);

        if ($existing === null) {
            $this->em->persist(
                new PriceRow($provider, $model, $data->validFrom, $data->unit->value, $data->rates, $data->currency)
            );

            return;
        }

        $existing->update($data->unit->value, $data->rates, $data->currency);
    }
}
