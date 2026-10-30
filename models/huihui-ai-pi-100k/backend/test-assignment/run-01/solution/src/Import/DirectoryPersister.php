<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника цен:
 * - провайдеры — по code;
 * - модели — по (provider, code);
 * - строки цен — по (provider, model, valid_from).
 *
 * Повторная запись тех же значений данные не изменяет.
 * Провайдер и модель обязаны существовать либо в файле, либо в БД
 * (для файла ретро-коррекции — достаточно БД).
 */
final class DirectoryPersister
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ImportException
     * @throws \DomainException
     *
     * @return array{
     *     providers: array<string, Provider>,
     *     models: array<string, Model>,
     *     rows: list<PriceRow>,
     *     priceRowsCreated: int,
     *     priceRowsUpdated: int,
     * }
     */
    public function apply(PriceDoc $doc): array
    {
        return $this->em->wrapInTransaction(function () use ($doc): array {
            /** @var array<string, Provider> $providers */
            $providers = [];
            foreach ($doc->providers as $docProvider) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $docProvider->code]);
                if ($provider === null) {
                    $provider = new Provider($docProvider->code, $docProvider->name, $docProvider->offPeak);
                    $this->em->persist($provider);
                } else {
                    if ($provider->getName() !== $docProvider->name) {
                        $provider->setName($docProvider->name);
                    }
                    $provider->setOffPeak($docProvider->offPeak);
                }
                $providers[$docProvider->code] = $provider;
            }

            /** @var array<string, Model> $models ключ — "providerCode|modelCode" */
            $models = [];
            foreach ($doc->models as $docModel) {
                $provider = $this->resolveProvider($docModel->provider, $providers);
                $key = $docModel->provider . '|' . $docModel->code;
                $existing = $this->em->getRepository(Model::class)->findOneBy([
                    'provider' => $provider,
                    'code' => $docModel->code,
                ]);
                if ($existing === null) {
                    $existing = new Model($provider, $docModel->code);
                    $this->em->persist($existing);
                }
                $models[$key] = $existing;
            }

            /** @var list<PriceRow> $rows */
            $rows = [];
            $created = 0;
            $updated = 0;
            foreach ($doc->rates as $docRow) {
                $provider = $this->resolveProvider($docRow->provider, $providers);
                $model = $this->resolveModel($docRow->provider, $docRow->model, $models);

                $row = $this->em->getRepository(PriceRow::class)->findOneBy([
                    'provider' => $provider,
                    'model' => $model,
                    'validFrom' => $docRow->validFrom,
                ]);

                if ($row === null) {
                    $row = new PriceRow($provider, $model, $docRow->validFrom, $docRow->currency, $docRow->unit, $docRow->rates);
                    $this->em->persist($row);
                    $created++;
                } elseif (
                    $row->getUnit() !== $docRow->unit
                    || $row->getCurrency() !== $docRow->currency
                    || !self::sameRates($row, $docRow->rates)
                ) {
                    $row->setCurrency($docRow->currency);
                    $row->applyUnitAndRates($docRow->unit, $docRow->rates);
                    $updated++;
                }

                $rows[] = $row;
            }

            $this->em->flush();

            return [
                'providers' => $providers,
                'models' => $models,
                'rows' => $rows,
                'priceRowsCreated' => $created,
                'priceRowsUpdated' => $updated,
            ];
        });
    }

    /**
     * @param array<string, Provider> $inFile
     *
     * @throws ImportException
     */
    private function resolveProvider(string $code, array $inFile): Provider
    {
        if (isset($inFile[$code])) {
            return $inFile[$code];
        }

        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($provider === null) {
            throw new ImportException(sprintf('Провайдер "%s" не найден в базе; сначала загрузите справочник цен (app:prices:import).', $code));
        }

        return $provider;
    }

    /**
     * @param array<string, Model> $inFile
     *
     * @throws ImportException
     */
    private function resolveModel(string $providerCode, string $modelCode, array $inFile): Model
    {
        $key = $providerCode . '|' . $modelCode;
        if (isset($inFile[$key])) {
            return $inFile[$key];
        }

        $provider = $this->resolveProvider($providerCode, []);
        $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
        if ($model === null) {
            throw new ImportException(sprintf('Модель "%s/%s" не найдена в базе; сначала загрузите справочник цен (app:prices:import).', $providerCode, $modelCode));
        }

        return $model;
    }

    /**
     * Численное сравнение ставок, где отсутствующая позиция считается нулем.
     *
     * @param array<string, string> $incoming
     */
    private static function sameRates(PriceRow $row, array $incoming): bool
    {
        $same = static fn (?string $stored, ?string $value): bool =>
            bccomp((string) ($stored ?? '0'), (string) ($value ?? '0'), 10) === 0;

        return $same($row->getInputRate(), $incoming['input'] ?? null)
            && $same($row->getOutputRate(), $incoming['output'] ?? null)
            && $same($row->getCacheReadRate(), $incoming['cache_read'] ?? null)
            && $same($row->getCacheWriteRate(), $incoming['cache_write'] ?? null)
            && $same($row->getAnyRate(), $incoming['any'] ?? null);
    }
}
