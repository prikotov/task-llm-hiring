<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника цен:
 * providers — по code, models — по (provider, code),
 * price_rows — по (provider, model, valid_from).
 *
 * Все изменения выполняются в одной транзакции: при ошибке
 * ни одна запись файла не сохраняется.
 */
final class PricesImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(PricesFile $file): Result
    {
        return $this->em->wrapInTransaction(function (EntityManagerInterface $em) use ($file) {
            $providers = $em->getRepository(Provider::class);
            $models = $em->getRepository(Model::class);
            $priceRows = $em->getRepository(PriceRow::class);

            $result = new Result();

            foreach ($file->providers() as $parsed) {
                $provider = $providers->findOneBy(['code' => $parsed->code()]);
                if ($provider === null) {
                    $provider = new Provider($parsed->code());
                    $em->persist($provider);
                }
                $provider->setName($parsed->name());
                $provider->setOffPeak($parsed->offPeak());
                $result->touchedProvider();
            }
            // ORM 4 не делает auto-flush перед запросами — фиксируем
            // провайдеров до запросов по моделям/ценам.
            $em->flush();

            foreach ($file->models() as $m) {
                $provider = $providers->findOneBy(['code' => $m['provider']]);
                if ($provider === null) {
                    throw new ImportException(sprintf(
                        'Модель "%s/%s": не найден провайдер "%s"',
                        $m['provider'],
                        $m['code'],
                        $m['provider'],
                    ));
                }
                if ($models->findOneBy(['provider' => $provider, 'code' => $m['code']]) === null) {
                    $em->persist(new Model($provider, $m['code']));
                }
                $result->touchedModel();
            }
            $em->flush(); // модели нужны для строк цен

            foreach ($file->prices() as $p) {
                $provider = $providers->findOneBy(['code' => $p['provider']]);
                if ($provider === null) {
                    throw new ImportException(sprintf(
                        'Строка цен: не найден провайдер "%s" (проверьте секции/предыдущие импорты)',
                        $p['provider'],
                    ));
                }
                $model = $models->findOneBy(['provider' => $provider, 'code' => $p['model']]);
                if ($model === null) {
                    throw new ImportException(sprintf(
                        'Строка цен: не найдена модель "%s/%s"',
                        $p['provider'],
                        $p['model'],
                    ));
                }

                $row = $priceRows->findOneBy([
                    'provider' => $provider,
                    'model' => $model,
                    'validFrom' => $p['valid_from'],
                ]);
                if ($row === null) {
                    $em->persist(new PriceRow(
                        $provider,
                        $model,
                        $p['valid_from'],
                        $p['currency'],
                        $p['unit'],
                        $p['rates'],
                    ));
                } else {
                    $row->apply($p['currency'], $p['unit'], $p['rates']);
                }
                $result->touchedPriceRow();
            }

            $em->flush();

            return $result;
        });
    }
}
