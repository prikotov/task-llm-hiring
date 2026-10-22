<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine;

use App\Billing\Domain\Catalog\Catalog;
use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\Provider;
use App\Billing\Infrastructure\Persistence\Doctrine\Entity\ModelRecord;
use App\Billing\Infrastructure\Persistence\Doctrine\Entity\ProviderRecord;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Реализация справочника на Doctrine ORM.
 *
 * Flush выполняется на границе use case (а не на каждом upsert): импорт
 * большого файла — это один flush, импорт с ошибкой — один откат.
 */
final class DoctrineCatalog implements Catalog
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function providers(): array
    {
        $records = $this->em->getRepository(ProviderRecord::class)->findBy([], ['code' => 'ASC']);

        return array_map(static fn (ProviderRecord $r): Provider => $r->toDomain(), $records);
    }

    public function provider(string $code): ?Provider
    {
        return $this->findProviderRecord($code)?->toDomain();
    }

    public function upsertProvider(Provider $provider): void
    {
        $record = $this->findProviderRecord($provider->code);

        if (null === $record) {
            $this->em->persist(ProviderRecord::fromDomain($provider));

            return;
        }

        $record->apply($provider);
    }

    public function upsertModel(ModelReference $model): void
    {
        $providerRecord = $this->findProviderRecord($model->providerCode)
            ?? throw new \RuntimeException(sprintf('Провайдер "%s" отсутствует в справочнике', $model->providerCode));

        if (null !== $this->findModelRecord($model)) {
            return;
        }

        $this->em->persist(ModelRecord::create($providerRecord, $model->modelCode));
    }

    public function hasModel(ModelReference $model): bool
    {
        return null !== $this->findModelRecord($model);
    }

    public function offPeakWindows(): array
    {
        $windows = [];

        foreach ($this->em->getRepository(ProviderRecord::class)->findAll() as $record) {
            $provider = $record->toDomain();

            if (null !== $provider->offPeak) {
                $windows[$provider->code] = $provider->offPeak;
            }
        }

        return $windows;
    }

    public function findModelRecord(ModelReference $model): ?ModelRecord
    {
        $providerRecord = $this->findProviderRecord($model->providerCode);

        if (null === $providerRecord) {
            return null;
        }

        return $this->em
            ->getRepository(ModelRecord::class)
            ->findOneBy(['provider' => $providerRecord, 'code' => $model->modelCode]);
    }

    private function findProviderRecord(string $code): ?ProviderRecord
    {
        return $this->em->getRepository(ProviderRecord::class)->findOneBy(['code' => $code]);
    }
}
