<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Catalog\ModelRepository;
use App\Domain\Catalog\PriceLine;
use App\Domain\Catalog\PriceLineRepository;
use App\Domain\Catalog\ProviderRepository;
use App\Domain\Common\Money;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

use function bcadd;
use function bcsub;
use function count;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Провайдер присылает исправленную строку цен. Уже рассчитанные события
 * пересчитываются по «новой» версии строки — с заменой их стоимости;
 * события, тарифицированные другими строками, тронуты не были.
 *
 * Механизм: события хранят ссылку на строку цен, по которой считались,
 * поэтому «заменённой строкой» однозначно определён набор событий
 * (та же (provider, model, valid_from); id строки не меняется).
 *
 * @return array{repriced:int, delta:string} — число пересчитанных событий
 *         и дельта (новая − старая) суммы их стоимостей
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceLineRepository $priceLines,
        private readonly RateCalculator $calculator,
    ) {
    }

    /** @param iterable<RepriceLine> $lines исправленные строки (уже распарсены) */
    public function reprice(iterable $lines): array
    {
        $repriced = 0;
        $delta = '0';

        $this->em->wrapInTransaction(function () use ($lines, &$repriced, &$delta): void {
            foreach ($lines as $entry) {
                $model = $this->findModelOrThrow($entry);
                $line = $this->priceLines->findPriceLine($model, $entry->validFrom());

                if ($line === null) {
                    // Новая строка: события под неё рассчитает app:billing:rate.
                    $line = new PriceLine($model->provider(), $model, $entry->validFrom());
                    $line->setPricingData($entry->currency(), $entry->scheme(), $entry->rates());
                    $this->em->persist($line);
                    continue;
                }

                if ($line->dataSignature() === $line->dataSignatureFor($entry->scheme()->value, $entry->rates())) {
                    // Строка не изменилась — пересчёта не требуется.
                    continue;
                }

                $events = $this->em->getRepository(UsageEvent::class)->findRatedByPriceLine($line);
                $oldSum = '0';
                foreach ($events as $event) {
                    /** @var UsageEvent $event */
                    $oldSum = bcadd($oldSum, (string) $event->cost(), Money::SCALE);
                }

                $line->setPricingData($entry->currency(), $entry->scheme(), $entry->rates());

                $newSum = '0';
                foreach ($events as $event) {
                    /** @var UsageEvent $event */
                    $cost = $this->calculator->eventCost($event, $line, $line->provider());
                    $event->resetCost($cost);
                    $newSum = bcadd($newSum, $cost, Money::SCALE);
                }

                $repriced += count($events);
                $delta = bcadd($delta, bcsub($newSum, $oldSum, Money::SCALE), Money::SCALE);
            }

            $this->em->flush();
        });

        return ['repriced' => $repriced, 'delta' => $delta];
    }

    private function findModelOrThrow(RepriceLine $entry): \App\Domain\Catalog\Model
    {
        $provider = $this->providers->findProviderByCode($entry->providerCode());
        if ($provider === null) {
            throw new \App\Common\RepriceException(
                'Провайдер «'.$entry->providerCode().'» отсутствует в справочнике',
            );
        }

        $model = $this->models->findModelByCode($provider, $entry->modelCode());
        if ($model === null) {
            throw new \App\Common\RepriceException(
                'Модель «'.$provider->code().'/'.$entry->modelCode().'» отсутствует в справочнике',
            );
        }

        return $model;
    }
}
