<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Domain\Catalog\Model;
use App\Billing\Domain\Catalog\Price;
use App\Billing\Domain\Catalog\Provider;
use App\Billing\Infrastructure\Persistence\ModelRepository;
use App\Billing\Infrastructure\Persistence\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника: провайдеры — по code, модели — по (provider, code),
 * строки цен — по (provider, model, valid_from).
 *
 * Строка цен, уже существующая с другими ставками, НЕ перезаписывается: история
 * цен, по которой уже тарифицированы события, меняется только через app:billing:reprice.
 */
final class PriceCatalogImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
    ) {
    }

    public function import(PriceCatalogData $data): void
    {
        $this->em->wrapInTransaction(function () use ($data): void {
            foreach ($data->providers as $p) {
                $provider = $this->em->find(Provider::class, $p['code']);
                if ($provider === null) {
                    $this->em->persist(new Provider($p['code'], $p['name'], $p['offPeak']));
                } else {
                    $provider->changeDetails($p['name'], $p['offPeak']);
                }
            }
            $this->em->flush();

            foreach ($data->models as $m) {
                $this->resolveModel($m['provider'], $m['code'], createIfMissing: true);
            }
            $this->em->flush();

            foreach ($data->prices as $p) {
                $model = $this->resolveModel($p['provider'], $p['model'], createIfMissing: true);
                $price = $this->prices->findOneByKey($model, $p['validFrom']);

                if ($price === null) {
                    $this->em->persist(new Price($model, $p['validFrom'], $p['currency'], $p['unit'], $p['rates']));
                    continue;
                }

                if (!$price->hasSameRates($p['unit'], $p['rates'])) {
                    throw new InvalidInputException(sprintf(
                        'Строка цен %s/%s с %s уже загружена с другими ставками; для исправления используйте app:billing:reprice',
                        $p['provider'],
                        $p['model'],
                        $p['validFrom']->format('Y-m-d\TH:i:s\Z'),
                    ));
                }
            }
            $this->em->flush();
        });
    }

    /**
     * Модель по (provider, code). При $createIfMissing создаёт её (секция models файла),
     * иначе — требует существования (ссылка из prices).
     */
    private function resolveModel(string $providerCode, string $code, bool $createIfMissing): Model
    {
        $model = $this->models->findOneByKey($providerCode, $code);
        if ($model !== null) {
            return $model;
        }

        $provider = $this->em->find(Provider::class, $providerCode);
        if ($provider === null) {
            throw new InvalidInputException(sprintf('Неизвестный провайдер: %s', $providerCode));
        }
        if (!$createIfMissing) {
            throw new InvalidInputException(sprintf('Неизвестная модель: %s/%s', $providerCode, $code));
        }

        $model = new Model($provider, $code);
        $this->em->persist($model);
        // flush нужен, чтобы следующие поиски в этом же импорте видели модель (DQL не делает auto-flush)
        $this->em->flush();

        return $model;
    }
}
