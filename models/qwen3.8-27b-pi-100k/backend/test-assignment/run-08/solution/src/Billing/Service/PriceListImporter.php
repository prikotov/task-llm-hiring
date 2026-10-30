<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Entity\Model;
use App\Billing\Entity\Provider;
use App\Billing\Entity\PriceRow;
use App\Billing\Import\Dto\PriceRowInput;
use App\Billing\Import\Dto\PricesFile;
use App\Billing\Repository\PriceRowRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника цен (команда app:prices:import):
 *
 *  - провайдеры — по code;
 *  - модели — по (provider, code);
 *  - строки цен — по (provider, model, valid_from).
 *
 * Повторная загрузка тех же данных не изменяет содержимое строк.
 */
final class PriceListImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceRowRepository $priceRows,
    ) {
    }

    public function import(PricesFile $file): void
    {
        $this->em->wrapInTransaction(function () use ($file): void {
            foreach ($file->providers as $input) {
                $provider = $this->em->find(Provider::class, $input->code);
                if ($provider === null) {
                    $provider = new Provider($input->code);
                    $this->em->persist($provider);
                }
                $provider->apply($input);
            }

            foreach ($file->models as $input) {
                $existing = $this->em->find(Model::class, [
                    'provider' => $input->provider,
                    'code' => $input->code,
                ]);
                if ($existing === null) {
                    $this->em->persist(new Model($input->provider, $input->code));
                }
            }

            foreach ($file->priceRows as $input) {
                $row = $this->priceRows->findByNaturalKey($input->provider, $input->model, $input->validFrom);
                if ($row === null) {
                    $row = new PriceRow(
                        0, // identity генерируется СУБД
                        $input->provider,
                        $input->model,
                        $input->validFrom,
                        $input->currency,
                        $input->price->unit->value,
                        self::encodeRates($input),
                    );
                    $this->em->persist($row);
                } else {
                    $row->apply($input);
                }
            }

            $this->em->flush();
        });
    }

    private static function encodeRates(PriceRowInput $input): string
    {
        $rates = [];
        foreach ($input->price->rates as $kind => $rate) {
            $rates[$kind] = $rate->value();
        }

        return json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
