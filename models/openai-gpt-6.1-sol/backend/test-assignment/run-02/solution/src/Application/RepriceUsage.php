<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\CostCalculator;
use App\Domain\UsageEvent;
use App\Infrastructure\Input;
use App\Infrastructure\PriceCatalog;
use App\Infrastructure\WriteTransaction;
use Doctrine\ORM\EntityManagerInterface;

final class RepriceUsage
{
    public function __construct(
        private EntityManagerInterface $em,
        private Input $input,
        private PriceCatalog $catalog,
        private CostCalculator $calculator,
        private WriteTransaction $transaction,
    ) {
    }

    /** @return array{count: int, delta: string} */
    public function run(string $file): array
    {
        $document = $this->input->prices($file, true);

        return $this->transaction->run(function () use ($document): array {
            $changed = $this->catalog->import($document, true);
            $ids = array_map(static fn ($price): int => $price->id, $changed);
            $count = $lastId = 0;
            $delta = '0.000000';
            if ($ids === []) {
                return compact('count', 'delta');
            }
            do {
                $events = $this->em->createQueryBuilder()->select('e', 'p')
                    ->from(UsageEvent::class, 'e')->join('e.ratedPrice', 'p')
                    ->where('p.id IN (:prices) AND e.id > :last AND e.costUsd IS NOT NULL')
                    ->setParameter('prices', $ids)->setParameter('last', $lastId)
                    ->orderBy('e.id', 'ASC')->setMaxResults(200)->getQuery()->getResult();
                foreach ($events as $event) {
                    $lastId = $event->id;
                    $adjustment = $event->reprice($this->calculator);
                    $this->em->persist($adjustment);
                    $delta = bcadd($delta, $adjustment->delta(), 6);
                    ++$count;
                }
                $this->em->flush();
                $this->em->clear();
            } while (count($events) === 200);

            return compact('count', 'delta');
        });
    }
}
