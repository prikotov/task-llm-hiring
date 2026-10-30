<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Pricing\Entity\Price;
use App\Domain\Pricing\Entity\ProviderModel;
use App\Domain\Pricing\Service\PricingCalculator;
use App\Domain\Provider\Entity\Provider;
use App\Domain\Usage\Entity\UsageEvent;
use App\Domain\Usage\ValueObject\RateStatus;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

final readonly class RatingService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PricingCalculator $calculator,
    ) {
    }

    public function ratePending(): RateResult
    {
        $eventRepository = $this->em->getRepository(UsageEvent::class);
        /** @var list<UsageEvent> $events */
        $events = $eventRepository->findBy(
            ['status' => RateStatus::Unrated],
            ['id' => 'ASC'],
        );

        $rated = 0;
        $unrated = [];
        $providerCache = [];
        $modelCache = [];
        $priceCache = [];
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        foreach ($events as $event) {
            $providerCode = $event->providerCode();
            if (!array_key_exists($providerCode, $providerCache)) {
                $providerCache[$providerCode] = $this->em->getRepository(Provider::class)
                    ->findOneBy(['code' => $providerCode]);
            }
            /** @var Provider|null $provider */
            $provider = $providerCache[$providerCode];

            if ($provider === null) {
                $unrated[] = $event;

                continue;
            }

            $modelKey = $providerCode."\0".$event->modelCode();
            if (!array_key_exists($modelKey, $modelCache)) {
                $modelCache[$modelKey] = $this->em->getRepository(ProviderModel::class)
                    ->findOneBy(['provider' => $provider, 'code' => $event->modelCode()]);
            }
            /** @var ProviderModel|null $model */
            $model = $modelCache[$modelKey];

            if ($model === null) {
                $unrated[] = $event;

                continue;
            }

            $cacheKey = $modelKey."\0".$event->occurredAt()->format('U');
            if (!array_key_exists($cacheKey, $priceCache)) {
                $priceCache[$cacheKey] = $this->findPrice($model, $event->occurredAt());
            }
            /** @var Price|null $price */
            $price = $priceCache[$cacheKey];

            if ($price === null) {
                $unrated[] = $event;

                continue;
            }

            $event->markRated($price, $this->calculator->calculate($event, $price), $now);
            ++$rated;
        }

        $this->em->flush();

        return new RateResult($rated, $unrated);
    }

    private function findPrice(ProviderModel $model, DateTimeImmutable $moment): ?Price
    {
        /** @var Price|null $price */
        $price = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Price::class, 'p')
            ->where('p.providerModel = :model')
            ->andWhere('p.validFrom <= :moment')
            ->setParameter('model', $model)
            ->setParameter('moment', $moment)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $price;
    }
}
