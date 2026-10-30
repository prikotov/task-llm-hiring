<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Entity\Model;
use App\Entity\Provider;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Entity\UsageRating;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

/**
 * Доступ к справочнику (провайдеры, модели, цены) и событиям использования.
 *
 * Валидация «сущность есть в базе» — здесь; формат входных данных — в
 * парсерах; бизнес-логика — в доменных сервисах.
 */
final class CatalogRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return Model[]
     */
    public function allModels(): array
    {
        return $this->em->getRepository(Model::class)->findAll();
    }

    public function providerByCode(string $code): ?Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
    }

    /**
     * @throws ImportException если модель не найдена в базе
     */
    public function modelByProviderAndCode(string $providerCode, string $code): Model
    {
        $provider = $this->providerByCode($providerCode);
        if ($provider === null) {
            throw ImportException::missingProvider($providerCode);
        }

        try {
            $qb = $this->em->createQueryBuilder()
                ->select('m')
                ->from(Model::class, 'm')
                ->join('m.provider', 'p')
                ->where('p.code = :pc')
                ->andWhere('m.code = :code')
                ->setParameter('pc', $providerCode)
                ->setParameter('code', $code);

            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException) {
            throw ImportException::missingModel($provider, $code);
        }
    }

    /**
     * Строка цены по (provider, model, valid_from) или null.
     */
    public function priceByProviderModelValidFrom(Provider $provider, Model $model, \DateTimeImmutable $validFrom): ?Price
    {
        try {
            $q = $this->em->getRepository(Price::class)->createQueryBuilder('pr')
                ->where('pr.provider = :prov')
                ->andWhere('pr.model = :md')
                ->andWhere('pr.validFrom = :vf')
                ->setParameter('prov', $provider)
                ->setParameter('md', $model)
                ->setParameter('vf', $validFrom)
                ->setMaxResults(1)
                ->getQuery();

            return $q->getOneOrNullResult();
        } catch (NoResultException) {
            return null;
        }
    }

    /**
     * Все строки цен модели, отсортированные по (valid_from, id) возрастанию.
     */
    public function pricesForModel(Model $model): array
    {
        return $this->em->getRepository(Price::class)->findBy(
            ['model' => $model],
            ['validFrom' => 'ASC', 'id' => 'ASC'],
        );
    }
}
