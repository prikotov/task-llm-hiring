<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\IsoTime;
use App\Entity\LlmModel;
use App\Entity\Price;
use App\Entity\Provider;
use App\Repository\LlmModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert каталога (провайдеры / модели / строки цен).
 */
final class PriceCatalogService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly LlmModelRepository $models,
        private readonly PriceRepository $prices,
    ) {
    }

    /**
     * @param array<int, array<string, mixed>>   $providersRaw секция providers
     * @param array<int, array<string, mixed>>   $modelsRaw    секция models
     * @param array<int, array<string, mixed>>   $pricesRaw    секция prices
     */
    public function import(array $providersRaw, array $modelsRaw, array $pricesRaw): void
    {
        foreach ($providersRaw as $row) {
            $this->upsertProvider($row);
        }

        $this->em->flush();

        foreach ($modelsRaw as $row) {
            $this->upsertModel($row);
        }

        $this->em->flush();

        foreach ($pricesRaw as $row) {
            $this->upsertPrice($row);
        }

        $this->em->flush();
    }

    /**
     * Upsert только строк цен (режим коррекции — reprice). Провайдеры и модели
     * должны уже существовать в справочнике; секции в файле коррекции могут
     * отсутствовать.
     *
     * @param array<int, array<string, mixed>> $pricesRaw
     * @return Price[] затронутые строки цен
     */
    public function upsertPriceRowsOnly(array $pricesRaw): array
    {
        $touched = [];
        foreach ($pricesRaw as $row) {
            $touched[] = $this->upsertPrice($row);
        }

        $this->em->flush();

        return $touched;
    }

    private function upsertProvider(array $row): Provider
    {
        $code = (string) ($row['code'] ?? '');
        if ($code === '') {
            throw new \RuntimeException('prices.import: provider without code');
        }

        $name = (string) ($row['name'] ?? $code);

        $op = $row['off_peak'] ?? null;

        /** @var Provider|null $provider */
        $provider = $this->providers->findByCode($code);
        if ($provider === null) {
            $provider = new Provider();
            $provider->setCode($code);
            $this->em->persist($provider);
        }

        $provider->setName($name);
        $provider->setOffPeak($op['window_start'] ?? null, $op['window_end'] ?? null, $op['multiplier'] ?? null);

        return $provider;
    }

    /**
     * @param array<string, string> $row
     */
    private function upsertModel(array $row): LlmModel
    {
        $providerCode = (string) ($row['provider'] ?? '');
        $code = (string) ($row['code'] ?? '');
        if ($providerCode === '' || $code === '') {
            throw new \RuntimeException('prices.import: model without provider/code');
        }

        $provider = $this->providers->findByCode($providerCode);
        if ($provider === null) {
            throw new \RuntimeException("prices.import: unknown provider '{$providerCode}'");
        }

        $model = $this->models->findByProviderAndCode($provider, $code);
        if ($model === null) {
            $model = new LlmModel();
            $model->setProvider($provider);
            $this->em->persist($model);
        }

        $model->setCode($code);

        return $model;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function upsertPrice(array $row): Price
    {
        $providerCode = (string) ($row['provider'] ?? '');
        $modelCode = (string) ($row['model'] ?? '');
        $validFromIso = (string) ($row['valid_from'] ?? '');
        $unit = (string) ($row['unit'] ?? '');
        $currency = (string) ($row['currency'] ?? 'USD');
        $rates = $row['rates'] ?? null;

        if ($providerCode === '' || $modelCode === '' || $validFromIso === '' || $unit === '') {
            throw new \RuntimeException('prices.import: price row missing fields');
        }

        if (!is_array($rates) || $rates === []) {
            throw new \RuntimeException('prices.import: price row missing rates');
        }

        // Ставки приводятся к десятичным строкам (без потери точности).
        $clean = [];
        foreach ($rates as $k => $v) {
            if (is_string($v)) {
                $val = $v;
            } elseif (is_int($v)) {
                $val = (string) $v;
            } elseif (is_float($v)) {
                // Только для экстренного случая (файлы задания — строки).
                $val = \number_format($v, 6, '.', '');
            } else {
                throw new \RuntimeException("prices.import: rate '{$k}' has wrong type");
            }
            $clean[(string) $k] = $val;
        }
        ksort($clean);

        $validFrom = IsoTime::parseUtc($validFromIso);

        $provider = $this->providers->findByCode($providerCode);
        if ($provider === null) {
            throw new \RuntimeException("prices.import: unknown provider '{$providerCode}'");
        }
        $model = $this->models->findByProviderAndCode($provider, $modelCode);
        if ($model === null) {
            throw new \RuntimeException("prices.import: unknown model '{$modelCode}'");
        }

        $price = $this->prices->findByKey($providerCode, $modelCode, $validFrom);
        if ($price === null) {
            $price = new Price();
            $price->setProvider($provider);
            $price->setModel($model);
            $this->em->persist($price);
        }

        $price->setValidFrom($validFrom);
        $price->setCurrency($currency);
        $price->setRates($unit, $clean);

        return $price;
    }
}
