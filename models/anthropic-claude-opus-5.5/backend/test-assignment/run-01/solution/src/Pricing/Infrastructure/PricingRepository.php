<?php

declare(strict_types=1);

namespace App\Pricing\Infrastructure;

use App\Pricing\Domain\LlmModel;
use App\Pricing\Domain\PriceRow;
use App\Pricing\Domain\Provider;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class PricingRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function provider(string $code): ?Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
    }

    public function model(string $providerCode, string $modelCode): ?LlmModel
    {
        $provider = $this->provider($providerCode);

        return $provider === null ? null
            : $this->em->getRepository(LlmModel::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
    }

    public function priceRow(LlmModel $model, DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->em->getRepository(PriceRow::class)->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
    }

    /** Строка с наибольшим valid_from <= $ts. */
    public function effectiveRow(string $providerCode, string $modelCode, DateTimeImmutable $ts): ?PriceRow
    {
        /** @var ?PriceRow */
        return $this->em->createQueryBuilder()
            ->select('r')->from(PriceRow::class, 'r')
            ->join('r.model', 'm')->join('m.provider', 'p')
            ->where('p.code = :p')->andWhere('m.code = :m')->andWhere('r.validFrom <= :ts')
            ->setParameter('p', $providerCode)->setParameter('m', $modelCode)->setParameter('ts', $ts, 'datetimetz_immutable')
            ->orderBy('r.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }
}
