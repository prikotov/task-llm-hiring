<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Справочник моделей (ключ — (provider, code)).
 */
final class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function byProviderAndCode(Provider $provider, string $modelCode): ?Model
    {
        return $this->findOneBy(['provider' => $provider, 'code' => $modelCode]);
    }

    public function upsert(string $providerCode, string $modelCode): Model
    {
        return $this->upsertWithProvider($providerCode, $modelCode, null);
    }

    public function upsertWithProvider(string $providerCode, string $modelCode, ?Provider $provider): Model
    {
        if ($provider === null) {
            $provider = $this->getEntityManager()->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
        }
        if ($provider === null) {
            throw new \DomainException(sprintf('Провайдер не найден: %s (импортируйте его до моделей)', $providerCode));
        }

        $existing = $this->byProviderAndCode($provider, $modelCode);
        if ($existing !== null) {
            return $existing;
        }

        if (strcasecmp((string) $provider->code, $providerCode) !== 0) {
            throw new \DomainException(sprintf('Код провайдера в модели не совпадает: %s и %s', $provider->code, $providerCode));
        }

        $model = new Model($provider, $modelCode);
        $this->getEntityManager()->persist($model);

        return $model;
    }
}
