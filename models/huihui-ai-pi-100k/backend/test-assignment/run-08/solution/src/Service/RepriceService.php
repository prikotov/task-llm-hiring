<?php

declare(strict_types=1);

namespace App\Service;

use App\DBAL\Types\TzDateStringType;
use App\Domain\Billing\MoneyMath;
use App\Domain\Billing\PricingCalculator;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\RepriceLogEntry;
use App\Entity\UsageEvent;
use App\Import\InputFormatException;
use App\Import\InputParser;

/**
 * Ретро-корекция цен (bonus: app:billing:reprice).
 *
 * Механизм — «перезапись под строк ценовых + журнал корректировки»:
 *  - строка (provider, model, valid_from) перезаписывается на новые значения;
 *  - события, тарифицированные именно под эту строку (priceValidFrom),
 *    пересчитываются под новые значения этой строки;
 *  - каждое преобразование фиксируется в reprice_log;
 *  - итоговый report не задваивается, т.к. стоимость события — одна запись.
 */
final class RepriceService
{
    public function __construct(
        private readonly \Doctrine\ORM\EntityManagerInterface $em,
        private readonly \App\Repository\UsageEventRepository $eventRepository,
    ) {
    }

    /**
     * @return array{events:int, delta:string}
     */
    public function apply(string $file): array
    {
        $prices = InputParser::priceFile($file)['prices'];
        if ($prices === []) {
            throw new InputFormatException('Correction file contains no «prices» rows.');
        }

        $em = $this->em;

        return $em->wrapInTransaction(function () use ($em, $prices): array {
            $events = 0;
            $delta = MoneyMath::zero();

            foreach ($prices as $row) {
                // Провайдер и модель должны существовать в базе.
                $provider = $em->getRepository(Provider::class)->find($row['provider']);
                $model = $em->getRepository(\App\Entity\Model::class)->find(
                    ['providerCode' => $row['provider'], 'code' => $row['model']]
                );
                if ($provider === null || $model === null) {
                    throw new InputFormatException(
                        sprintf('Price row %s/%s: provider or model not found in DB.', $row['provider'], $row['model'])
                    );
                }

                $existing = $em->getRepository(Price::class)->findOneBy([
                    'providerCode' => $row['provider'],
                    'modelCode' => $row['model'],
                    'validFrom' => $row['valid_from'],
                ]);

                if ($existing === null) {
                    // Новая строка — событий под неё ещё нет, просто вставляем.
                    $price = new Price();
                    $this->applyValues($price, $row);
                } else {
                    $this->applyValues($existing, $row);
                    $price = $existing;

                    $affected = $this->eventRepository->createQueryBuilder('e')
                        ->where('e.status = :status')
                        ->andWhere('e.providerCode = :provider')
                        ->andWhere('e.modelCode = :model')
                        ->andWhere('e.priceValidFrom = :validFrom')
                        ->setParameter('status', UsageEvent::STATUS_RATED)
                        ->setParameter('provider', $row['provider'])
                        ->setParameter('model', $row['model'])
                        ->setParameter('validFrom', $row['valid_from'])
                        ->getQuery()
                        ->getResult();

                    foreach ($affected as $event) {
                        if (! $event instanceof UsageEvent) {
                            continue;
                        }

                        $oldCost = (string) $event->costUsd;
                        $newCost = PricingCalculator::computeEventCost(
                            $event->tokenCounts(),
                            $price->unit,
                            $price->rates,
                            $provider->offPeak(),
                            new \DateTimeImmutable($event->ts),
                        );

                        if ($oldCost === $newCost) {
                            continue;
                        }

                        $event->costUsd = $newCost;
                        $delta = MoneyMath::add($delta, MoneyMath::sub($newCost, $oldCost));
                        $events++;

                        $log = new RepriceLogEntry();
                        $log->providerCode = $event->providerCode;
                        $log->providerRequestId = $event->providerRequestId;
                        $log->oldCostUsd = $oldCost;
                        $log->newCostUsd = $newCost;
                        $log->deltaUsd = MoneyMath::sub($newCost, $oldCost);
                        $log->appliedAt = TzDateStringType::canonize(new \DateTimeImmutable('now'));
                        $em->persist($log);
                    }
                }

                $em->persist($price);
            }

            $em->flush();

            return ['events' => $events, 'delta' => MoneyMath::formatTo6($delta)];
        });
    }

    /** @param array{provider:string, model:string, valid_from:\DateTimeImmutable, currency:string, unit:string, rates:array<string,string>} $row */
    private function applyValues(Price $price, array $row): void
    {
        $price->providerCode = $row['provider'];
        $price->modelCode = $row['model'];
        $price->validFrom = TzDateStringType::canonize($row['valid_from']);
        $price->currency = $row['currency'];
        $price->unit = $row['unit'];
        $price->rates = $row['rates'];
    }
}
