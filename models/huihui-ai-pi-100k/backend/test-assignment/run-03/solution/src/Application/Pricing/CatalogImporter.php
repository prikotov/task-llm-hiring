<?php

declare(strict_types=1);

namespace App\Application\Pricing;

use App\Application\Exception\FileFormatException;
use App\Domain\Common\Iso8601Utc as Utc;
use App\Domain\Pricing\TokenKind;
use App\Persistence\Entity\Model;
use App\Persistence\Entity\PriceRow;
use App\Persistence\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка справочника цен (upsert):
 *  — провайдеры по code;
 *  — модели по (provider, code);
 *  — строки цен по (provider, model, valid_from).
 *
 * Весь документ применяется в одной транзакции: некорректный документ
 * (ссылка на неизвестный провайдера/модель) либо целиком принят,
 * либо целиком отклонён.
 */
final class CatalogImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @throws FileFormatException при ссылке на неизвестную сущность
     * @return array{providers: int, models: int, price_rows: int} — число обработанных записей секций
     */
    public function import(PriceDocument $document): array
    {
        return $this->em->wrapInTransaction(function () use ($document): array {
            $providers = 0;
            foreach ($document->providers as $spec) {
                $entity = $this->providerRepository()->findOneBy(['code' => $spec->code]) ?? new Provider();
                if ($entity->id === null) {
                    $entity->code = $spec->code;
                    $this->em->persist($entity);
                }
                $entity->name = $spec->name;
                $entity->offPeakStart = $spec->offPeak?->startMinutes();
                $entity->offPeakEnd = $spec->offPeak?->endMinutes();
                $entity->offPeakMultiplier = $spec->offPeak?->multiplier();
                $providers++;
            }

            // Промежуточный flush: новые строки должны быть видны последующим запросам.
            $this->em->flush();

            $models = 0;
            foreach ($document->models as $spec) {
                $entity = $this->model($spec->providerCode, $spec->code) ?? new Model();
                if ($entity->id === null) {
                    $entity->provider = $this->requireProvider($spec->providerCode, 'models');
                    $entity->code = $spec->code;
                    $this->em->persist($entity);
                }
                $models++;
            }

            $this->em->flush();

            $rows = 0;
            foreach ($document->priceRows as $spec) {
                $entity = $this->findPriceRow($spec) ?? new PriceRow();
                if ($entity->id === null) {
                    $entity->provider = $this->requireProvider($spec->providerCode, 'prices');
                    $entity->model = $this->requireModel($spec->providerCode, $spec->modelCode, 'prices');
                    $entity->validFrom = Utc::toMutableUtc($spec->validFrom);
                    $this->em->persist($entity);
                }
                $entity->currency = $spec->currency;
                $entity->unit = $spec->unit->value;
                $entity->rateInput = $spec->rates[TokenKind::Input->value] ?? null;
                $entity->rateOutput = $spec->rates[TokenKind::Output->value] ?? null;
                $entity->rateCacheRead = $spec->rates[TokenKind::CacheRead->value] ?? null;
                $entity->rateCacheWrite = $spec->rates[TokenKind::CacheWrite->value] ?? null;
                $entity->rateAny = $spec->rates[TokenKind::Any->value] ?? null;
                $rows++;
            }

            $this->em->flush();

            return ['providers' => $providers, 'models' => $models, 'price_rows' => $rows];
        });
    }

    private function findPriceRow(PriceRowSpec $spec): ?PriceRow
    {
        return $this->em->createQueryBuilder()
            ->select('pr')
            ->from(PriceRow::class, 'pr')
            ->join('pr.provider', 'p')
            ->join('pr.model', 'm')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('pr.validFrom = :validFrom')
            ->setParameter('provider', $spec->providerCode)
            ->setParameter('model', $spec->modelCode)
            ->setParameter('validFrom', Utc::toMutableUtc($spec->validFrom))
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function providerRepository(): \Doctrine\ORM\EntityRepository
    {
        return $this->em->getRepository(Provider::class);
    }

    private function requireProvider(string $code, string $section): Provider
    {
        $provider = $this->providerRepository()->findOneBy(['code' => $code]);
        if ($provider === null) {
            throw FileFormatException::invalidFile($section, sprintf('неизвестный провайдер "%s"', $code));
        }

        return $provider;
    }

    private function requireModel(string $providerCode, string $code, string $section): Model
    {
        $model = $this->model($providerCode, $code);
        if ($model === null) {
            throw FileFormatException::invalidFile($section, sprintf('неизвестная пара провайдер/модель: "%s/%s"', $providerCode, $code));
        }

        return $model;
    }

    private function model(string $providerCode, string $code): ?Model
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(Model::class, 'm')
            ->join('m.provider', 'p')
            ->where('p.code = :provider')
            ->andWhere('m.code = :code')
            ->setParameter('provider', $providerCode)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
