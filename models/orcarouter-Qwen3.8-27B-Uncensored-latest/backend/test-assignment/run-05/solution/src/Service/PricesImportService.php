<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Model;
use App\Domain\PriceRow;
use App\Domain\Provider;
use App\Support\ImportError;
use App\Support\PriceFile;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт справочника: провайдеры, модели, строки цен (идемпотентный upsert).
 *
 * Ключи: провайдеры — по code; модели — по (provider, code);
 * строки цен — по (provider, model, valid_from).
 */
final class PricesImportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(string $path): void
    {
        $data = PriceFile::parse($path);

        $this->em->wrapInTransaction(function () use ($data): void {
            $providers = $this->upsertProviders($data['providers']);
            $models = $this->upsertModels($data['models'], $providers);
            $this->upsertPrices($data['prices'], $providers, $models);
        });
    }

    /**
     * @param list<array{code: string, name: string, off_peak: ?array{window_start: string, window_end: string, multiplier: string}}> $list
     * @return array<string, Provider> code => entity
     */
    private function upsertProviders(array $list): array
    {
        $map = [];
        foreach ($list as $p) {
            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $p['code']]);
            if ($provider === null) {
                $provider = Provider::make($p['code'], $p['name']);
                $this->em->persist($provider);
            }
            $provider->setName($p['name']);
            if ($p['off_peak'] !== null) {
                $provider->setOffPeak($p['off_peak']['window_start'], $p['off_peak']['window_end'], $p['off_peak']['multiplier']);
            } else {
                $provider->setOffPeak(null, null, null);
            }
            $map[$p['code']] = $provider;
        }

        return $map;
    }

    /**
     * @param list<array{provider: string, code: string}> $list
     * @param array<string, Provider> $providers
     * @return array<string, Model> "provider|code" => entity
     */
    private function upsertModels(array $list, array $providers): array
    {
        $map = [];
        foreach ($list as $m) {
            $provider = $providers[$m['provider']] ?? null;
            if ($provider === null) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $m['provider']]);
            }
            if ($provider === null) {
                throw new ImportError(sprintf('models "%s/%s": провайдер "%s" не найден', $m['provider'], $m['code'], $m['provider']));
            }

            $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $m['code']]);
            if ($model === null) {
                $model = Model::make($provider, $m['code']);
                $this->em->persist($model);
            }
            $map[$m['provider'].'|'.$m['code']] = $model;
        }

        return $map;
    }

    /**
     * @param list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: \App\Domain\BillingUnit, rates: array<string, string>}> $list
     * @param array<string, Provider> $providers
     * @param array<string, Model> $models
     */
    private function upsertPrices(array $list, array $providers, array $models): void
    {
        foreach ($list as $p) {
            $provider = $providers[$p['provider']] ?? null;
            if ($provider === null) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $p['provider']]);
            }
            if ($provider === null) {
                throw new ImportError(sprintf('prices "%s/%s": провайдер "%s" не найден', $p['provider'], $p['model'], $p['provider']));
            }

            $key = $p['provider'].'|'.$p['model'];
            $model = $models[$key] ?? null;
            if ($model === null) {
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $p['model']]);
            }
            if ($model === null) {
                throw new ImportError(sprintf('prices "%s/%s": модель не найдена', $p['provider'], $p['model']));
            }

            $row = $this->em->getRepository(PriceRow::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $p['valid_from'],
            ]);
            if ($row === null) {
                $row = PriceRow::make($provider, $model, $p['valid_from']);
                $this->em->persist($row);
            }
            $row->update($p['valid_from'], $p['currency'], $p['unit'], $p['rates']);
        }
    }
}
