<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\RateCalculator;
use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Usage\EventStatus;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий (pending), включая
 * повторные попытки для ранее unrated (цены могли подтянуться позже).
 *
 * Расчёт: точная десятичная арифметика (bcmath), единственное округление —
 * стоимость события до 6 знаков HALF_UP. Строка цены — та, у которой
 * наибольшее valid_from <= ts события.
 */
final class RatingService
{
    use UsesTransactions;
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * @return array{rated: int, unrated: list<UsageEvent>}
     */
    public function rateUnrated(): array
    {
        $providers = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $provider) {
            $providers[$provider->code] = $provider;
        }

        $modelsById = [];
        foreach ($this->em->getRepository(Model::class)->findAll() as $model) {
            $modelsById[$model->id] = $model;
        }

        /** @var array<string, list<PriceRow>> $rowsByKey (ключ — provider|code модели), validFrom DESC */
        $rowsByKey = [];
        foreach ($this->em->getRepository(PriceRow::class)->findAll() as $row) {
            $model = $modelsById[$row->model] ?? null;
            if ($model === null) {
                continue;
            }
            $rowsByKey[$model->provider.'|'.$model->code][] = $row;
        }
        foreach ($rowsByKey as $key => $list) {
            usort($list, static fn (PriceRow $a, PriceRow $b): int => $b->validFrom <=> $a->validFrom);
            $rowsByKey[$key] = $list;
        }

        $unrated = [];
        $rated = 0;
        $now = new \DateTimeImmutable();

        $this->inTransaction(function () use ($providers, $rowsByKey, &$unrated, &$rated, $now): void {
            foreach ($this->em->getRepository(UsageEvent::class)->findBy(['status' => EventStatus::Pending->value]) as $event) {
                $rows = $rowsByKey[$event->provider.'|'.$event->modelCode] ?? [];
                $row = null;
                foreach ($rows as $candidate) {
                    if ($candidate->validFrom <= $event->ts) {
                        $row = $candidate;
                        break; // list sorted DESC: первый подходящий — максимальный valid_from <= ts
                    }
                }

                if ($row === null) {
                    $unrated[] = $event;
                    continue;
                }

                $provider = $providers[$event->provider] ?? null;
                $cost = RateCalculator::eventCost(
                    $event,
                    $row->unit,
                    $row->rates(),
                    OffPeakWindow::fromProvider($provider) ?? null,
                    $provider?->offPeakMultiplier,
                );

                $event->markRated($row->id, $cost, $now);
                ++$rated;
            }
        });

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
