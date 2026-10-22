<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Decimal;
use App\Domain\Entity\OffPeakWindow;
use App\Domain\Entity\PriceRow;
use App\Domain\Entity\UsageEvent;
use App\Domain\Repository\ModelRepository;
use App\Domain\Repository\PriceRowRepository;
use App\Domain\Repository\ProviderRepository;
use App\Domain\Repository\UsageEventRepository;
use App\Infrastructure\Pricer;

/**
 * Тарификация событий использования.
 *
 * Повторно тарифицирует все ещё unreated события (в т.ч. ранее rated, но
 * тарифицированные по уже изменённой (reprice) строке цены).
 */
final class PricingService
{
    /** @var string[] предупреждения об оставшихся unreated событиях (в stderr) */
    private array $unratedWarnings = [];

    /** @var string[] id пересчитанных событий (для reprice) */
    private array $repricedIds = [];

    public function __construct(
        private Pricer $pricer,
        private PriceRowRepository $priceRows,
        private ProviderRepository $providers,
        private ModelRepository $models,
        private UsageEventRepository $events,
    ) {
    }

    /**
     * Тарификация всех ещё не тарифицированных событий в периоде.
     *
     * @param \DateTimeImmutable|null $from начало периода, включительно; null — без фильтра
     * @param \DateTimeImmutable|null $to конец периода, исключительно; null — без фильтра
     * @return array{rated:int, unrated:int}
     */
    public function rate(?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        /** @var UsageEvent[] $events — все ещё не тарифицированные */
        $events = $this->events->findUnrated($from, $to);

        // предупреждения от предыдущего запуска не наследуются.
        $this->unratedWarnings = [];
        $this
            ->repricedIds = [];

        $rated = 0;
        $unrated = 0;

        foreach ($events as $event) {
            $priceRow = null === $event->priceRowId
                ? null
                : $this->priceRows->findForId($event->priceRowId);

            $cost = $this->priceRow($priceRow, $event);
            $newPriceRowId = $this->currentPriceRowId($event);

            if (null === $cost) {
                ++$unrated;
                continue;
            }

            // Если строка цены успела измениться (reprice), cost уже свежий —
            // priceRowId обновляем. Иначе — cost уже был верен, priceRowId тот же.
            $event->rated = true;
            $event->costUsd = $cost;
            $event->priceRowId = $newPriceRowId ?? $event->priceRowId;
            $this->events->save($event);

            ++$rated;
        }

        return ['rated' => $rated, 'unrated' => $unrated];
    }

    /**
     * Перетарификация Rated-событий, тарифицированных по одной из указанных
     * (уже вписанных в БД) строк цены.
     *
     * @param PriceRow[] $replacedPriceRows
     * @return array{repricedEvents:string[]|string[], delta:string}
     */
    public function reprice(array $priceRows): array
    {
        $this->repricedIds = [];

        $ids = array_map(
            static fn (PriceRow $row) => $row->getId(),
            $priceRows,
        );
        /** @var UsageEvent[] $affected — rated события с одной из строк цены */
        $affected = $this->events->findByPriceRowIds($ids);

        $sumOld = new Decimal('0');
        $sumNew = new Decimal('0');

        foreach ($affected as $event) {
            $oldCost = $event->costUsd ?? '0';
            $priceRow = $this->priceRows->findForId($event->priceRowId);

            $sumOld = $sumOld->add($oldCost);

            $newCost = $this->priceRow($priceRow, $event);
            if (null !== $newCost) {
                $sumNew = $sumNew->add($newCost);
            }

            $event->rated = true;
            $event->costUsd = $newCost;
            $this->events->save($event);
            $this->repricedIds[] = (string) $event->getId();
        }

        $delta = Decimal::halfUp($sumNew->subtract($sumOld)->toString());

        return [
            'repricedEvents' => $this->repricedIds,
            'delta' => $delta,
        ];
    }

    /**
     * @return string[]
     */
    public function getUnratedWarnings(): array
    {
        return $this->unratedWarnings;
    }

    /**
     * @return string[] id пересчитанных событий
     */
    public function getRepricedIds(): array
    {
        return $this->repricedIds;
    }

    /**
     * @return string|null стоимость с округлением; null — unrated
     */
    private function priceRow(?PriceRow $priceRow, UsageEvent $event): ?string
    {
        if (null === $priceRow) {
            $this->unratedWarnings[] = $this->unratedLine($event);

            return null;
        }

        return $this->pricer->price(
            $priceRow,
            $this->offPeakFor($event),
            $event->ts,
            $event->tokensInput,
            $event->tokensOutput,
            $event->tokensCacheRead,
            $event->tokensCacheWrite,
        );
    }

    private function offPeakFor(UsageEvent $event): OffPeakWindow
    {
        $provider = $this->providers->findOneByCode($event->providerId);

        return $provider?->getOffPeak() ?? new OffPeakWindow(null, null, null);
    }

    private function currentPriceRowId(UsageEvent $event): ?int
    {
        $model = $this->models->findByCode(
            $event->providerId,
            $this->modelCode($event),
        );
        if (null === $model) {
            return null;
        }

        $priceRow = $this->priceRows->findActiveFor($model, $event->ts);

        return null === $priceRow ? null : $priceRow->getId();
    }

    private function modelCode(UsageEvent $event): string
    {
        // $event->model хранится как '<provider>/<code>'; извлекаем код.
        $parts = explode('/', $event->model, 2);

        return $parts[1] ?? $event->model;
    }

    private function unratedLine(UsageEvent $event): string
    {
        return 'UNRATED ' . $event->providerId . '/' . $event->providerRequestId
            . ' ' . $event->ts->format('c') . ' no_price';
    }
}
