<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Entity\Model;
use App\Domain\Entity\Provider;
use App\Domain\Repository\ModelRepository;
use App\Domain\Repository\PriceRowRepository;
use App\Domain\Repository\ProviderRepository;
use App\Infrastructure\PriceParser;

/**
 * Загрузка истории цен ( провайдеры, модели, строки цен) из JSON-файла.
 *
 * Идемпотентный upsert:
 *   - провайдеры — по `code`;
 *   - модели — по `(provider, code)`;
 *   - строки цены — по `(provider, model, valid_from)`.
 *
 * Провайдер и модель должны существовать либо в том же файле, либо в уже
 * загруженном справочнике (модель не может ссылаться на неизвестного
 * провайдера).
 */
final class PriceImporter
{
    /** @var Provider[] код -> сущность */
    private array $providers = [];

    public function __construct(
        private PriceParser $parser,
        private ProviderRepository $providerRepo,
        private ModelRepository $modelRepo,
        private PriceRowRepository $priceRows,
    ) {
    }

    /**
     * @return array{providers:int, models:int, prices:int}
     */
    public function importFromFile(string $filePath): array
    {
        return $this->import($this->readFile($filePath));
    }

    /**
     * @return array{providers:int, models:int, prices:int}
     */
    public function import(string $content): array
    {
        $data = $this->parser->parse($content);

        $this->providers = [];
        foreach ($data['providers'] as $p) {
            /** @var Provider|null $entity */
            $entity = $this->providerRepo->findOneByCode($p['code']);
            if (null === $entity) {
                /** @var Provider $entity */
                $entity = new Provider();
            }
            $entity->code = $p['code'];
            $entity->name = $p['name'];
            $entity->offPeakStart = $p['off_peak_start'];
            $entity->offPeakEnd = $p['off_peak_end'];
            $entity->offPeakMultiplier = $p['off_peak_multiplier'];
            $this->providerRepo->save($entity);
            $this->providers[$p['code']] = $entity;
        }

        $modelEntities = [];
        foreach ($data['models'] as $m) {
            $provider = $this->providers[$m['provider']]
                ?? $this->providerRepo->findOneByCode($m['provider']);
            if (null === $provider) {
                throw new \InvalidArgumentException(
                    'Модель "' . $m['code'] . '" ссылается на неизвестного провайдера "'
                        . $m['provider'] . '".'
                );
            }

            /** @var Model|null $entity */
            $entity = $this->modelRepo->findByCode($m['provider'], $m['code']);
            if (null === $entity) {
                /** @var Model $entity */
                $entity = new Model();
                $entity->provider = $provider;
            }
            $entity->code = $m['code'];
            $this->modelRepo->save($entity);
            $modelEntities[$m['provider'] . '|' . $m['code']] = $entity;
        }

        // Додзагружаем уже существующие модели из справочника.
        foreach ($this->modelRepo->findAll() as $existing) {
            $modelEntities[$existing->provider->code . '|' . $existing->code] = $existing;
        }

        $upserted = 0;
        foreach ($data['prices'] as $row) {
            $provider = $this->providers[$row['provider']]
                ?? $this->providerRepo->findOneByCode($row['provider']);
            $model = $modelEntities[$row['provider'] . '|' . $row['model']]
                ?? $this->modelRepo->findByCode($row['provider'], $row['model']);

            if (null === $provider || null === $model) {
                throw new \InvalidArgumentException(
                    'Ставка для модели "' . $row['model'] . '" ссылается на '
                        . 'известные справочник провайдера/модели.'
                );
            }

            $validFrom = new \DateTimeImmutable($row['valid_from']);
            $changed = $this->priceRows->upsert(
                $provider,
                $model,
                $validFrom,
                $row['currency'],
                $row['unit'],
                $row['rates']['input'] ?? null,
                $row['rates']['output'] ?? null,
                $row['rates']['cache_read'] ?? null,
                $row['rates']['cache_write'] ?? null,
                $row['rates']['any'] ?? null,
            );

            if ($changed) {
                ++$upserted;
            }
        }

        return [
            'providers' => \count($data['providers']),
            'models' => \count($data['models']),
            'prices' => $upserted,
        ];
    }

    /**
     * @return PriceRow[] строки, изменённые в результате закладки
     */
    public function reimportPricesFromFile(string $filePath): array
    {
        /** @var PriceRow[] $replaced */
        $replaced = [];
        $data = $this->parser->parse($this->readFile($filePath));

        // Додзагружаем существующих провайдеров/моделй в кэш resolve.
        $providers = [];
        foreach ($this->providerRepo->findAll() as $provider) {
            $providers[$provider->code] = $provider;
        }
        foreach ($data['providers'] as $p) {
            if (isset($providers[$p['code']])) {
                continue;
            }
            /** @var Provider $entity */
            $entity = new Provider();
            $entity->code = $p['code'];
            $entity->name = $p['name'];
            $entity->offPeakStart = $p['off_peak_start'];
            $entity->offPeakEnd = $p['off_peak_end'];
            $entity->offPeakMultiplier = $p['off_peak_multiplier'];
            $this->providerRepo->save($entity);
            $this->providers[$p['code']] = $entity;
            $providers[$p['code']] = $entity;
        }

        $models = [];
        foreach ($this->modelRepo->findAll() as $model) {
            $models[$model->provider->code . '|' . $model->code] = $model;
        }
        foreach ($data['models'] as $m) {
            if (isset($models[$m['provider'] . '|' . $m['code']])) {
                continue;
            }
            $provider = $providers[$m['provider']] ?? null;
            if (null === $provider) {
                throw new \InvalidArgumentException(
                    'Модель "' . $m['code'] . '" ссылается на незнакомого провайдера "'
                        . $m['provider'] . '".'
                );
            }
            /** @var Model $entity */
            $entity = new Model();
            $entity->provider = $provider;
            $entity->code = $m['code'];
            $this->modelRepo->save($entity);
            $models[$m['provider'] . '|' . $m['code']] = $entity;
        }

        foreach ($data['prices'] as $row) {
            $provider = $providers[$row['provider']] ?? null;
            $model = $models[$row['provider'] . '|' . $row['model']] ?? null;
            if (null === $provider || null === $model) {
                throw new \InvalidArgumentException(
                    'Строка цены для модели "' . $row['model']
                        . '" ссылается на известную модель/провайдера.'
                );
            }

            $validFrom = new \DateTimeImmutable($row['valid_from']);
            $changed = $this->priceRows->upsert(
                $provider,
                $model,
                $validFrom,
                $row['currency'],
                $row['unit'],
                $row['rates']['input'] ?? null,
                $row['rates']['output'] ?? null,
                $row['rates']['cache_read'] ?? null,
                $row['rates']['cache_write'] ?? null,
                $row['rates']['any'] ?? null,
            );
            if ($changed) {
                $replaced[] = $this->priceRows->findForTriple($provider, $model, $validFrom);
            }
        }

        return $replaced;
    }

    private function readFile(string $filePath): string
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException('Файл истории цен не найден или недоступен: ' . $filePath);
        }
        $content = file_get_contents($filePath);
        if (false === $content) {
            throw new \RuntimeException('Не удалось прочитать файл истории цен: ' . $filePath);
        }

        return $content;
    }
}
