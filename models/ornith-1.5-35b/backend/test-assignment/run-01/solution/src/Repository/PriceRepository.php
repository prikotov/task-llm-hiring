<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * История цен: выбор строки цены для конкретного события.
 *
 * Правило выбора (п. 1): берётся строка той же ( провайдер, модель) с
 * наибольшим valid_from, который не превышает момент события ts
 * (valid_from включительно). Если таких строк нет — событие unrated
 * (возвращает null).
 */
final class PriceRepository
{
    /** @var array<int, EntityRepository<Price>> */
    private array $repoByModelId = [];

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function priceFor(UsageEvent $event): ?Price
    {
        $instant = $event->getInstant();

        $provider = $this->em->getRepository(Provider::class)->find($event->getProvider()->getId());
        if ($provider === null) {
            return null;
        }

        $model = $this->em->getRepository(Model::class)
            ->findOneBy(['provider' => $provider, 'code' => $event->getModel()->getCode()]);
        if ($model === null) {
            return null;
        }

        /** @var list<Price> $rows */
        $rows = $this->rowsFor($model);

        $applicable = null;
        foreach ($rows as $price) {
            $from = $price->getValidFrom();
            if ($from > $instant->dateTime()) {
                continue;
            }
            if ($applicable === null || $from > $applicable->getValidFrom()) {
                $applicable = $price;
            }
        }

        return $applicable;
    }

    private function rowsFor(Model $model): array
    {
        $key = $model->getId();
        if (!isset($this->repoByModelId[$key])) {
            /** @var EntityRepository<Price> $repo */
            $repo = $this->em->getRepository(Price::class);
            $this->repoByModelId[$key] = $repo;
        }

        $repo = $this->repoByModelId[$key];

        /** @var list<Price> $rows */
        return $repo->findBy(['model' => $model], ['valid_from' => 'DESC']);
    }
}
