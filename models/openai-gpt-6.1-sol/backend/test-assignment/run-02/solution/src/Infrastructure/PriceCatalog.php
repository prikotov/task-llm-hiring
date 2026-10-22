<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Model;
use App\Domain\Price;
use App\Domain\Provider;
use App\Domain\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

final class PriceCatalog
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /** Вызывается внутри WriteTransaction. Возвращает только действительно изменённые существующие цены. */
    public function import(array $document, bool $correction = false): array
    {
        if (!$correction) {
            foreach ($document['providers'] as $row) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $row['code']]);
                if ($provider === null) {
                    $provider = new Provider($row['code'], $row['name'], $row['off_peak']);
                    $this->em->persist($provider);
                } else {
                    $provider->update($row['name'], $row['off_peak']);
                }
            }
            $this->em->flush();
            foreach ($document['models'] as $row) {
                $provider = $this->provider($row['provider']);
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $row['code']]);
                if ($model === null) {
                    $this->em->persist(new Model($provider, $row['code']));
                }
            }
            $this->em->flush();
        }

        $changed = [];
        foreach ($document['prices'] as $row) {
            $provider = $this->provider($row['provider']);
            $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $row['model']]);
            if ($model === null) {
                throw new \InvalidArgumentException('Неизвестная модель: ' . $row['provider'] . '/' . $row['model']);
            }
            $price = $this->em->getRepository(Price::class)->findOneBy(['model' => $model, 'validFrom' => $row['date']]);
            if ($price === null) {
                $price = new Price($model, $row['date'], $row['currency'], $row['unit'], $row['rates']);
                $this->em->persist($price);
            } elseif ($price->replace($row['currency'], $row['unit'], $row['rates'])) {
                $changed[] = $price;
            }
        }
        $this->em->flush();

        return $changed;
    }

    public function priceFor(UsageEvent $event): ?Price
    {
        return $this->em->createQueryBuilder()
            ->select('p', 'm', 'provider')
            ->from(Price::class, 'p')
            ->join('p.model', 'm')
            ->join('m.provider', 'provider')
            ->where('provider.code = :provider AND m.code = :model AND p.validFrom <= :at')
            ->setParameter('provider', $event->providerCode)
            ->setParameter('model', $event->modelCode)
            ->setParameter('at', $event->occurredAt, 'utc_instant')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    private function provider(string $code): Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $code])
            ?? throw new \InvalidArgumentException('Неизвестный провайдер: ' . $code);
    }
}
