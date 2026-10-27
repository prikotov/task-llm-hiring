<?php

declare(strict_types=1);

namespace App\Service;

use App\DBAL\Types\TzDateStringType;
use App\Import\InputFormatException;
use App\Import\InputParser;
use App\Repository\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Идемпотентный upsert справочника:
 *  провайдеры — по code; модели — по (provider, code);
 *  строки цен — по (provider, model, valid_from).
 */
final class PriceCatalogService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceRepository $priceRepository,
    ) {
    }

    /** @throws InputFormatException */
    public function import(string $file): void
    {
        $data = InputParser::priceFile($file);
        $em = $this->em;

        $em->wrapInTransaction(function () use ($data, $em): void {
            $fileProviders = [];
            $fileModels = [];
            foreach ($data['providers'] as $p) {
                $fileProviders[$p['code']] = $p;
            }
            foreach ($data['models'] as $m) {
                $fileModels[$m['provider'].'|'.$m['code']] = true;
            }

            foreach ($data['providers'] as $row) {
                $provider = $em->getRepository(\App\Entity\Provider::class)->find($row['code']) ?? new \App\Entity\Provider();
                $provider->code = $row['code'];
                $provider->name = $row['name'];

                $offPeak = $row['off_peak'];
                $provider->offPeakWindowStart = $offPeak['start'] ?? null;
                $provider->offPeakWindowEnd = $offPeak['end'] ?? null;
                $provider->offPeakMultiplier = $offPeak['multiplier'] ?? null;

                $em->persist($provider);
            }

            foreach ($data['models'] as $row) {
                $model = $em->getRepository(\App\Entity\Model::class)->find(
                    ['providerCode' => $row['provider'], 'code' => $row['code']]
                ) ?? new \App\Entity\Model();
                $model->providerCode = $row['provider'];
                $model->code = $row['code'];
                $em->persist($model);
            }

            foreach ($data['prices'] as $row) {
                // Провайдер и модель должны существовать (в файле или уже в базе).
                $this->assertEntityExists($row['provider'], $row['model'], $fileProviders, $fileModels);

                $price = $this->priceRepository->findByRow($row['provider'], $row['model'], $row['valid_from'])
                    ?? new \App\Entity\Price();
                $price->providerCode = $row['provider'];
                $price->modelCode = $row['model'];
                $price->validFrom = TzDateStringType::canonize($row['valid_from']);
                $price->currency = $row['currency'];
                $price->unit = $row['unit'];
                $price->rates = $row['rates'];

                $em->persist($price);
            }

            $em->flush();
        });
    }

    /** @param array<string,array{code:string,name:string,off_peak:?array}> $fileProviders @param array<string,bool> $fileModels */
    private function assertEntityExists(string $provider, string $model, array $fileProviders, array $fileModels): void
    {
        $em = $this->em;
        $providerOk = isset($fileProviders[$provider])
            || $em->getRepository(\App\Entity\Provider::class)->find($provider) !== null;
        $modelOk = isset($fileModels[$provider.'|'.$model])
            || $em->getRepository(\App\Entity\Model::class)->find(
                ['providerCode' => $provider, 'code' => $model]
            ) !== null;

        if (!$providerOk || !$modelOk) {
            throw new InputFormatException(
                sprintf('Price row for %s/%s: provider and model must exist (in file or already in DB).', $provider, $model),
            );
        }
    }

    /** @throws NonUniqueResultException */
    public function provider(string $code): ?\App\Entity\Provider
    {
        return $this->em->getRepository(\App\Entity\Provider::class)->find($code);
    }
}
