<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\Common\Input;
use App\Domain\Common\ImportFormatException;
use App\Domain\Pricing\RepriceLine;
use Doctrine\ORM\EntityManagerInterface;

use function json_decode;

/**
 * Импорт справочника цен: провайдеры, модели, строки цен.
 *
 * Идемпотентный upsert:
 *  - провайдеры — по `code`;
 *  - модели — по `(provider, code)`;
 *  - строки цен — по `(provider, model, valid_from)`.
 */
final class PricesImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceLineRepository $priceLines,
    ) {
    }

    /** @param string $path путь к JSON-файлу */
    public function importFile(string $path): void
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportFormatException('Не удалось прочитать файл: '.$path);
        }

        $json = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        if (!\is_array($json) || $json === []) {
            throw new ImportFormatException('JSON: корневой элемент должен быть объектом');
        }

        $this->em->wrapInTransaction(function () use ($path, $json): void {
            $this->import($json, $path);
        });
    }

    /** @param array<string, mixed> $json */
    private function import(array $json, string $path): void
    {
        $providers = $json['providers'] ?? [];
        $models = $json['models'] ?? [];
        $prices = $json['prices'] ?? [];

        if (!\is_array($providers) || !\is_array($models) || !\is_array($prices)) {
            throw new ImportFormatException("{$path}: секции providers/models/prices должны быть массивами");
        }

        $fileProviders = $this->importProviders($providers, $path);
        $fileModels = $this->importModels($models, $fileProviders, $path);

        foreach ($prices as $i => $price) {
            $this->importPrice($price, $i, $fileModels, $path);
        }
    }

    /** @param array<int, mixed> $rows @return array<string, Provider> */
    private function importProviders(array $rows, string $path): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            $label = "{$path}: providers[{$i}]";
            $row = Input::asRow($row, $label);

            $code = Input::asNonEmptyString($row['code'] ?? null, $label.'[code]');
            $name = Input::asNonEmptyString($row['name'] ?? null, $label.'[name]');

            $startMin = null;
            $endMin = null;
            $multiplier = null;
            $offPeak = $row['off_peak'] ?? null;
            if ($offPeak !== null) {
                $offPeak = Input::asRow($offPeak, $label.'[off_peak]');

                $ws = (string) ($offPeak['window_start'] ?? '');
                if (!Input::isHhmm($ws)) {
                    throw new ImportFormatException($label.'[off_peak.window_start]: ожидается "HH:MM" (UTC)');
                }
                $we = (string) ($offPeak['window_end'] ?? '');
                if (!Input::isHhmm($we)) {
                    throw new ImportFormatException($label.'[off_peak.window_end]: ожидается "HH:MM" (UTC)');
                }
                $multiplier = (string) ($offPeak['multiplier'] ?? '');
                if (!Input::isDecimalString($multiplier)) {
                    throw new ImportFormatException($label.'[off_peak.multiplier]: ожидается неотрицательное десятичное');
                }

                $startMin = Input::hhmmToMinutes($ws);
                $endMin = Input::hhmmToMinutes($we);
            }

            $provider = $this->providers->findProviderByCode($code);
            if ($provider === null) {
                $provider = new Provider($code);
                $this->em->persist($provider);
                $this->em->flush();
            }

            $provider->setName($name);
            $provider->setOffPeak($startMin, $endMin, $multiplier);

            $result[$code] = $provider;
        }

        return $result;
    }

    /**
     * @param array<int, mixed> $rows
     * @param array<string, Provider> $fileProviders
     * @return array<string, Model> ключ "<provider>/<code>"
     */
    private function importModels(array $rows, array $fileProviders, string $path): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            $label = "{$path}: models[{$i}]";
            $row = Input::asRow($row, $label);

            $providerCode = Input::asNonEmptyString($row['provider'] ?? null, $label.'[provider]');
            $code = Input::asNonEmptyString($row['code'] ?? null, $label.'[code]');

            $provider = $fileProviders[$providerCode]
                ?? $this->providers->findProviderByCode($providerCode);
            if ($provider === null) {
                throw new ImportFormatException($label.': неизвестный провайдер «'.$providerCode.'»');
            }

            $model = $this->models->findModelByCode($provider, $code);
            if ($model === null) {
                $model = new Model($provider, $code);
                $this->em->persist($model);
                $this->em->flush();
            }

            $result[$provider->code().'/'.$code] = $model;
        }

        return $result;
    }

    /** @param array<string, Model> $fileModels */
    private function importPrice(mixed $row, int $i, array $fileModels, string $path): void
    {
        $label = "{$path}: prices[{$i}]";
        $row = Input::asRow($row, $label);

        $entry = RepriceLine::fromArray($row, $label);

        $model = $fileModels[$entry->providerCode().'/'.$entry->modelCode()] ?? null;
        if ($model === null) {
            // Модель могла быть загружена в предыдущем импорте.
            $provider = $this->providers->findProviderByCode($entry->providerCode());
            if ($provider !== null) {
                $model = $this->models->findModelByCode($provider, $entry->modelCode());
            }
        }
        if ($model === null) {
            throw new ImportFormatException(
                $label.': неизвестная модель «'.$entry->providerCode().'/'.$entry->modelCode().'»',
            );
        }

        $line = $this->priceLines->findPriceLine($model, $entry->validFrom());
        if ($line === null) {
            $line = new PriceLine($model->provider(), $model, $entry->validFrom());
            $this->em->persist($line);
        }
        $line->setPricingData($entry->currency(), $entry->scheme(), $entry->rates());
    }
}
