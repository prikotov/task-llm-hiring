<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Parsers\PricesFile;
use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\RateCalculator;
use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Exception\ImportException;
use App\Domain\Usage\EventStatus;
use App\Domain\Usage\RepriceEntry;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус): провайдер присылает исправленную строку
 * цены (тот же формат файла); уже рассчитанные события, тарифицированные
 * данной строкой (provider, model, valid_from), пересчитываются по новым
 * тарифам. События, не рассчитанные этой строкой, не затрагиваются;
 * unrated-события не трогаются (будут рассчитаны при следующем
 * `billing:rate` с новой строкой).
 *
 * Механизм: перезапись стоимости события + неизменяемый журнал
 * reprice_entries (по одной записи на факт корректировки).
 */
final class RepriceService
{
    use UsesTransactions;
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * @return array{events: int, delta: string} delta — новая минус старая сумма, 6 знаков
     */
    public function reprice(string $json): array
    {
        $data = PricesFile::parse($json, true);

        /** @var array<int, int> ids событий по факту (для точного n) */
        $touched = [];
        $delta = '0';

        $this->inTransaction(function () use ($data, &$touched, &$delta): void {
            foreach ($data['prices'] as $price) {
                $provider = $this->em->getRepository(Provider::class)->find($price['provider']);
                if ($provider === null) {
                    throw ImportException::invalid(
                        'billing:reprice',
                        sprintf('провайдер "%s" не найден в справочнике', $price['provider']),
                    );
                }

                $model = $this->em->getRepository(Model::class)->findOneBy([
                    'provider' => $price['provider'],
                    'code' => $price['model'],
                ]);
                if ($model === null) {
                    throw ImportException::invalid(
                        'billing:reprice',
                        sprintf('модель "%s/%s" не найдена в справочнике', $price['provider'], $price['model']),
                    );
                }

                // upsert по (provider, model, valid_from)
                $this->em->flush();
                $row = $this->em->getRepository(PriceRow::class)->findOneBy([
                    'model' => $model->id,
                    'validFrom' => $price['valid_from'],
                ]);
                if ($row === null) {
                    $row = new PriceRow();
                    $row->model = $model->id;
                    $row->validFrom = $price['valid_from'];
                    $this->em->persist($row);
                }
                $row->currency = $price['currency'];
                $row->unit = $price['unit'];
                $row->rates = json_encode($price['rates'], JSON_THROW_ON_ERROR);
                $this->em->flush(); // id новой строки и новые тарифы видны дальше

                if ($row->id === null) {
                    continue;
                }

                // события, рассчитанные именно этой строкой
                $events = $this->em->getRepository(UsageEvent::class)->findBy([
                    'status' => EventStatus::Rated->value,
                    'priceRowId' => $row->id,
                ]);

                $offPeak = OffPeakWindow::fromProvider($provider);

                foreach ($events as $event) {
                    if ($event->priceRowId !== $row->id) {
                        continue;
                    }

                    $oldCost = (string) $event->costUsd;
                    $newCost = RateCalculator::eventCost(
                        $event,
                        $row->unit,
                        $row->rates(),
                        $offPeak,
                        $provider->offPeakMultiplier,
                    );

                    $delta = \App\Domain\Billing\Money::sub($delta, $oldCost);
                    $delta = \App\Domain\Billing\Money::add($delta, $newCost);

                    $event->costUsd = \App\Domain\Billing\Money::format6($newCost);
                    $event->ratedAt = new \DateTimeImmutable();
                    $touched[$event->id] = 1;

                    $entry = new RepriceEntry();
                    $entry->usageEvent = (int) $event->id;
                    $entry->priceRow = (int) $row->id;
                    $entry->oldCostUsd = $oldCost;
                    $entry->newCostUsd = \App\Domain\Billing\Money::format6($newCost);
                    $entry->createdAt = new \DateTimeImmutable();
                    $this->em->persist($entry);
                }
            }
        });

        return ['events' => \count($touched), 'delta' => \App\Domain\Billing\Money::format6($delta)];
    }
}
