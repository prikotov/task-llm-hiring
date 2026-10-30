<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Catalog\CatalogFile;
use App\Domain\Catalog\ProviderSpec;
use App\Domain\CatalogParser;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка справочника (provides, models, price_rows).
 *
 * Upsert по естественным ключам:
 *  - провайдер — code;
 *  - модель — (provider, code);
 *  - строка цены — (provider, model, valid_from).
 *
 * Повторная загрузка тех же значений записи не изменяет (сравнение
 * числовых полей — bccomp, «0.5» и «0.500000» равны).
 *
 * Ссылки строк цен разрешаются против секций файла, а при отсутствии —
 * против уже загруженного справочника; непрояснённые ссылки — ошибка.
 */
final class PriceCatalogImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogParser $parser,
    ) {
    }

    public function import(string $filePath): void
    {
        $catalog = $this->parser->parse(self::readFile($filePath));

        $this->em->wrapInTransaction(function () use ($catalog): void {
            $providers = $this->upsertProviders($catalog->providers);
            $this->em->flush(); // новые провайдеры получают идентификаторы для следующих запросов

            $models = $this->upsertModels($catalog->models, $providers);
            $this->em->flush(); // новые модели получают идентификаторы для запросов строк цен

            $this->upsertPriceRows($catalog->prices, $providers, $models);
            $this->em->flush();
        });
    }

    /**
     * @param list<ProviderSpec> $specs
     *
     * @return array<string, Provider> code => сущность
     */
    private function upsertProviders(array $specs): array
    {
        $repository = $this->em->getRepository(Provider::class);
        $map = [];

        foreach ($specs as $spec) {
            $provider = $repository->findOneBy(['code' => $spec->code]);
            if ($provider === null) {
                $provider = new Provider($spec->code, $spec->name);
                $this->em->persist($provider);
            } else {
                $provider->setName($spec->name);
            }

            if ($spec->offPeak === null) {
                $provider->setOffPeak(null, null, null);
            } else {
                $provider->setOffPeak($spec->offPeak['start'], $spec->offPeak['end'], $spec->offPeak['multiplier']);
            }

            $map[$spec->code] = $provider;
        }

        return $map;
    }

    /**
     * @param list<\App\Domain\Catalog\ModelSpec> $specs
     * @param array<string, Provider> $providers
     *
     * @return array<string, Model> «providerCode\0modelCode» => сущность
     */
    private function upsertModels(array $specs, array $providers): array
    {
        $map = [];

        foreach ($specs as $spec) {
            $provider = $providers[$spec->provider]
                ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $spec->provider]);
            if ($provider === null) {
                throw new ImportException(
                    "Модель \"{$spec->provider}/{$spec->code}\": провайдер \"{$spec->provider}\" "
                    . 'не найден — и в файле, и в справочнике'
                );
            }

            $existing = $this->em->createQuery(
                'SELECT m FROM ' . Model::class . ' m WHERE m.provider = :provider AND m.code = :code'
            )
                ->setParameter('provider', $provider)
                ->setParameter('code', $spec->code)
                ->getOneOrNullResult();

            if ($existing === null) {
                $existing = new Model($provider, $spec->code);
                $this->em->persist($existing);
            }

            $map[$spec->provider . "\0" . $spec->code] = $existing;
        }

        return $map;
    }

    /**
     * @param list<\App\Domain\Catalog\PriceSpec> $specs
     * @param array<string, Provider> $providers
     * @param array<string, Model> $models
     */
    private function upsertPriceRows(array $specs, array $providers, array $models): void
    {
        foreach ($specs as $spec) {
            $key = $spec->provider . "\0" . $spec->model;
            $model = $models[$key]
                ?? $this->em->createQuery(
                    'SELECT m FROM ' . Model::class . ' m JOIN m.provider p WHERE p.code = :provider AND m.code = :code'
                )
                    ->setParameter('provider', $spec->provider)
                    ->setParameter('code', $spec->model)
                    ->getOneOrNullResult();

            if ($model === null) {
                throw new ImportException(
                    "Строка цены \"{$spec->provider}/{$spec->model}\": модель не найдена — "
                    . 'загрузите провайдера и модель в справочник'
                );
            }

            $existing = $this->em->createQuery(
                'SELECT pr FROM ' . PriceRow::class . ' pr WHERE pr.model = :model AND pr.validFrom = :validFrom'
            )
                ->setParameter('model', $model)
                ->setParameter('validFrom', $spec->validFrom)
                ->getOneOrNullResult();

            if ($existing === null) {
                $this->em->persist(new PriceRow($model, $spec));
            } else {
                $existing->replaceWith($spec);
            }
        }
    }

    private static function readFile(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException("Файл не найден или недоступен: {$path}");
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException("Не удалось прочитать файл: {$path}");
        }

        return $raw;
    }
}
