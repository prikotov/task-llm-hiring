<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\CostCalculator;
use App\Domain\UsageEvent;
use App\Infrastructure\Input;
use App\Infrastructure\PriceCatalog;
use App\Infrastructure\WriteTransaction;
use Doctrine\ORM\EntityManagerInterface;

final class RateUsage
{
    public function __construct(
        private EntityManagerInterface $em,
        private PriceCatalog $catalog,
        private CostCalculator $calculator,
        private WriteTransaction $transaction,
    ) {
    }

    /** @return array{rated: int, warnings: list<string>} */
    public function run(): array
    {
        return $this->transaction->run(function (): array {
            $rated = $lastId = 0;
            $warnings = [];
            do {
                $events = $this->em->createQueryBuilder()->select('e')->from(UsageEvent::class, 'e')
                    ->where('e.costUsd IS NULL AND e.id > :last')->setParameter('last', $lastId)
                    ->orderBy('e.id', 'ASC')->setMaxResults(200)->getQuery()->getResult();
                foreach ($events as $event) {
                    $lastId = $event->id;
                    $price = $this->catalog->priceFor($event);
                    if ($price === null) {
                        $warnings[] = 'UNRATED ' . $event->providerCode . '/' . $event->providerRequestId . ' '
                            . Input::displayInstant($event->occurredAt) . ' no_price';
                        continue;
                    }
                    $event->rate($price, $this->calculator);
                    ++$rated;
                }
                $this->em->flush();
                $this->em->clear();
            } while (count($events) === 200);

            return compact('rated', 'warnings');
        });
    }
}
