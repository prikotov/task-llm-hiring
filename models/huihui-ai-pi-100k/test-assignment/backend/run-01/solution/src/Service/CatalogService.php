<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Операции со справочником (провайдеры, модели, строки цен).
 */
final class CatalogService
{
    public function providerByCode(EntityManagerInterface $em, string $code): ?\App\Entity\Provider
    {
        return $em->createQuery(
            'SELECT p FROM '.\App\Entity\Provider::class.' p WHERE p.code = :code'
        )
            ->setParameter('code', $code)
            ->getOneOrNullResult();
    }

    public function modelByCode(EntityManagerInterface $em, int $providerId, string $code): ?\App\Entity\Model
    {
        return $em->createQuery(
            'SELECT m FROM '.\App\Entity\Model::class.' m JOIN m.provider p WHERE p.id = :pid AND m.code = :code'
        )
            ->setParameter('pid', $providerId)
            ->setParameter('code', $code)
            ->getOneOrNullResult();
    }

    /**
     * @param bool $offPeakSpecified true — в файле было поле off_peak (включая явную null);
     *                               false — поле отсутствовало, существующее окно сохраняем
     */
    public function upsertProvider(EntityManagerInterface $em, string $code, string $name, ?\App\Domain\OffPeak $offPeak, bool $offPeakSpecified = true): \App\Entity\Provider
    {
        $provider = $this->providerByCode($em, $code);
        $isNew = $provider === null;
        if ($isNew) {
            $provider = new \App\Entity\Provider();
            $provider->code = $code;
            $em->persist($provider);
        }

        $provider->name = $name;
        if (!$offPeakSpecified) {
            // поле off_peak в файле отсутствовало — существующее окно сохраняем
        } elseif ($offPeak !== null && $offPeak->isConfigured()) {
            $provider->offPeakWindowStart = $offPeak->minutesStart();
            $provider->offPeakWindowEnd = $offPeak->minutesEnd();
            $provider->offPeakMultiplier = $offPeak->multiplier();
        } else {
            $provider->offPeakWindowStart = null;
            $provider->offPeakWindowEnd = null;
            $provider->offPeakMultiplier = null;
        }

        if ($isNew) {
            $em->flush(); // получить id до использования
        }

        return $provider;
    }

    /**
     * @return int модель id
     */
    public function upsertModel(EntityManagerInterface $em, int $providerId, string $code): int
    {
        $model = $this->modelByCode($em, $providerId, $code);
        if ($model === null) {
            $provider = $em->find(\App\Entity\Provider::class, $providerId);
            if ($provider === null) {
                throw new \App\Domain\DomainException(sprintf('Неизвестный провайдер id=%d', $providerId));
            }
            $model = new \App\Entity\Model();
            $model->provider = $provider;
            $model->code = $code;
            $em->persist($model);
            $em->flush(); // получить id до создания строк цен
        }

        return $model->id;
    }
}
