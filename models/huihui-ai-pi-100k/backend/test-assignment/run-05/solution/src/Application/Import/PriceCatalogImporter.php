<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\Exception\InputException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт справочника цен (команда app:prices:import).
 *
 * 1. Разбираем и валидируем весь файл (безопаснее: некорректный файл
 *    не оставляет половинчатых данных).
 * 2. Проверяем, что провайдеры и модели строк цен существуют
 *    (в файле или уже в БД).
 * 3. Идемпотентно применяем изменения в одной транзакции.
 *
 * @throws InputException
 */
final class PriceCatalogImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceFileParser $parser,
        private readonly CatalogUpsert $upsert,
    ) {
    }

    public function import(string $path): void
    {
        $file = $this->parser->parse($path);
        $this->assertReferencedCatalog($file);

        $em = $this->em;
        $em->getConnection()->transactional(function () use ($file): void {
            foreach ($file->providers as $provider) {
                $this->upsert->upsertProvider($provider);
            }
            foreach ($file->models as $model) {
                $this->upsert->upsertModel($model);
            }
            foreach ($file->prices as $price) {
                $this->upsert->upsertPriceRow($price);
            }
            $this->em->flush();
        });
    }

    private function assertReferencedCatalog(ParsedPrices $file): void
    {
        $providerInFile = [];
        foreach ($file->providers as $p) {
            $providerInFile[$p->code] = true;
        }

        $modelInFile = [];
        foreach ($file->models as $m) {
            $modelInFile[$m->provider . '|' . $m->code] = true;
        }

        $providerRepo = $this->em->getRepository(\App\Infrastructure\Persistence\Entity\Provider::class);
        $modelRepo = $this->em->getRepository(\App\Infrastructure\Persistence\Entity\Model::class);

        foreach ($file->models as $i => $record) {
            $label = sprintf('models[%d]', $i);

            if (
                !isset($providerInFile[$record->provider])
                && $providerRepo->findOneBy(['code' => $record->provider]) === null
            ) {
                throw new InputException(sprintf(
                    '%s: провайдер "%s" отсутствует в справочнике (в файле или в БД)',
                    $label, $record->provider,
                ));
            }
        }

        foreach ($file->prices as $i => $record) {
            $label = sprintf('prices[%d]', $i);

            if (
                !isset($providerInFile[$record->provider])
                && $providerRepo->findOneBy(['code' => $record->provider]) === null
            ) {
                throw new InputException(sprintf(
                    '%s: провайдер "%s" отсутствует в справочнике (в файле или в БД)',
                    $label, $record->provider,
                ));
            }

            if (
                !isset($modelInFile[$record->provider . '|' . $record->model])
                && $modelRepo->findOneBy(['provider' => $record->provider, 'code' => $record->model]) === null
            ) {
                throw new InputException(sprintf(
                    '%s: модель "%s/%s" отсутствует в справочнике (в файле или в БД)',
                    $label, $record->provider, $record->model,
                ));
            }
        }
    }
}
