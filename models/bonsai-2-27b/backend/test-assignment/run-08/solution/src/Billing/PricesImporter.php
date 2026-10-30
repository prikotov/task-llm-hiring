<?php

declare(strict_types=1);

namespace App\Billing;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\QueryException;
use Doctrine\Persistence\Exception\PersistentObjectsException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use App\Entity\Provider;
use App\Entity\OffPeak;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Billing\Math\PricingEngine;
use App\Billing\Math\MoneyMath;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Style;
use App\Billing\Exception\ImportException;
use App\Billing\Exception\ImportException as ImportException;

/**
 * Конвейер импорта JSON-файла цен: providers, models, prices.
 *
 * Идемпотентный upsert по:
 *   - provider: code
 *   - model: (provider, code)
 *   - price: (provider, model, valid_from)
 */
final class PricesImporter
{
    public function __construct(
        private readonly \App\Entity\Repository\RepositoryFactory $factory,
        private readonly PricingEngine $engine,
    ) {
    }

    /**
     * @throws ImportException
     */
    public function import(string $json): array
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $providers = $data['providers'] ?? [];
        $models = $data['models'] ?? [];
        $prices = $data['prices'] ?? [];

        // 1. Providers
        foreach ($providers as $p) {
            $this->upsertProvider($p);
        }

        // 2. Models
        foreach ($models as $m) {
            $this->upsertModel($m);
        }

        // 3. Prices
        foreach ($prices as $pr) {
            $this->upsertPrice($pr);
        }

        return ['providers' => count($providers), 'models' => count($models), 'prices' => count($prices)];
    }

    private function upsertProvider(array $p): void
    {
        $code = trim((string) ($p['code'] ?? ''));
        if ($code === '') {
            throw new ImportException("Provider: код пуст");
        }

        $provider = $this->em()->getRepository(Provider::class)
            ->findOne(['code' => $code]);

        if ($provider !== null) {
            $provider->setName((string) ($p['name'] ?? $provider->getName()));
            $this->applyOffPeak($provider, $p['off_peak'] ?? null);
        } else {
            $provider = new Provider();
            $provider->setCode($code);
            $provider->setName((string) ($p['name'] ?? 'Unknown'));
            $this->applyOffPeak($provider, $p['off_peak'] ?? null);
            $this->em()->persist($provider);
        }

        $this->em()->flush();
    }

    private function applyOffPeak(Provider $provider, ?array $offPeak): void
    {
        if ($offPeak === null) {
            $provider->setOffPeak(null);
            return;
        }

        $windowStart = $offPeak['window_start'] ?? null;
        $windowEnd = $offPeak['window_end'] ?? null;
        $multiplier = $offPeak['multiplier'] ?? null;

        if ($windowStart === null || $windowEnd === null) {
            $provider->setOffPeak(null);
            return;
        }

        $multiplier = $multiplier !== null ? $multiplier : '1.000000';
        $provider->setOffPeak(new OffPeak(
            $windowStart,
            $windowEnd,
            $multiplier
        ));
    }

    private function upsertModel(array $m): void
    {
        $providerCode = trim((string) ($m['provider'] ?? ''));
        $code = trim((string) ($m['code'] ?? ''));
        if ($providerCode === '' || $code === '') {
            throw new ImportException("Model: код провайдера или модели пуст");
        }

        $provider = $this->em()->getRepository(Provider::class)
            ->findOne(['code' => $providerCode]);
        if ($provider === null) {
            throw new ImportException("Model: провайдер $providerCode не найден");
        }

        $model = $this->em()->getRepository(Model::class)
            ->findOne([
                'providerCode' => $providerCode,
                'code' => $code,
            ]);

        if ($model === null) {
            $model = new Model($provider, $code);
            $this->em()->persist($model);
        }

        $this->em()->flush();
    }

    private function upsertPrice(array $pr): void
    {
        $providerCode = trim((string) ($pr['provider'] ?? ''));
        $modelCode = trim((string) ($pr['model'] ?? ''));
        $validFrom = trim((string) ($pr['valid_from'] ?? ''));
        if ($providerCode === '' || $modelCode === '' || $validFrom === '') {
            throw new ImportException("Price: поля provider/model/valid_from обязательны");
        }

        $provider = $this->em()->getRepository(Provider::class)
            ->findOne(['code' => $providerCode]);
        if ($provider === null) {
            throw new ImportException("Price: провайдер $providerCode не найден");
        }

        $model = $this->em()->getRepository(Model::class)
            ->findOne([
                'providerCode' => $providerCode,
                'code' => $modelCode,
            ]);
        if ($model === null) {
            throw new ImportException("Price: модель $modelCode не найдена");
        }

        $price = $this->em()->getRepository(Price::class)
            ->findOne([
                'providerCode' => $providerCode,
                'modelCode' => $modelCode,
                'validFrom' => $validFrom,
            ]);

        $currency = (string) ($pr['currency'] ?? 'USD');
        $unit = (string) ($pr['unit'] ?? '');
        $rates = $pr['rates'] ?? [];

        if ($unit === '') {
            throw new ImportException("Price: unit обязателен");
        }

        if ($price === null) {
            $price = new Price();
            $this->em()->persist($price);
        }

        $price->setProviderCode($providerCode);
        $price->setModelCode($modelCode);
        $price->setValidFrom($validFrom);
        $price->setCurrency($currency);
        $price->setUnit($unit);
        $price->setModel($model);
        $price->setRates($rates);

        $this->em()->flush();
    }
}
