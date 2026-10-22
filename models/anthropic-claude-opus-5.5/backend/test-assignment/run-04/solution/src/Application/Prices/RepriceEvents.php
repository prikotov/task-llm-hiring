<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Domain\Billing\CostCalculator;
use App\Domain\Money\Decimal;
use App\Domain\Pricing\CostAdjustment;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PricingUnit;
use App\Infrastructure\Doctrine\Entity\PriceRowEntity;
use App\Infrastructure\Doctrine\Repository\CostAdjustmentRepository;
use App\Infrastructure\Doctrine\Repository\ModelRepository;
use App\Infrastructure\Doctrine\Repository\PriceRowRepository;
use App\Infrastructure\Doctrine\Repository\ProviderRepository;
use App\Infrastructure\Doctrine\Repository\UsageEventRepository;
use App\Infrastructure\Json\PriceListData;
use App\Infrastructure\Json\PriceListReader;
use App\Infrastructure\Json\UsageEventReader;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

/**
 * Ретро-коррекция цен.
 *
 * Механизм: строки цен обновляются на месте (upsert по provider+model+valid_from),
 * затем пересчитываются все уже рассчитанные события, которые были
 * тарифицированы затронутыми строками. Каждая переоценка пишется в журнал
 * cost_adjustments (старое значение, новое, причина), а в событии
 * остаётся только актуальная стоимость — поэтому отчёт не задваивает суммы.
 */
final class RepriceEvents
{
    public function __construct(
        private readonly PriceListReader $reader,
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRowRepository $priceRows,
        private readonly UsageEventRepository $events,
        private readonly CostAdjustmentRepository $adjustments,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * @return array{repriced: int, delta: Decimal}
     */
    public function __invoke(string $path, string $reason = 'retro price correction'): array
    {
        $data = $this->reader->read($path);

        $this->em->beginTransaction();
        try {
            $affectedModels = $this->applyCorrections($data);
            $this->em->flush();

            [$repriced, $delta] = $this->recalculate($affectedModels, $reason);

            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }

        return ['repriced' => $repriced, 'delta' => $delta];
    }

    /**
     * Применяет исправленные строки. Провайдеры и модели ищутся в уже
     * загруженном справочнике (секции providers/models в файле могут
     * отсутствовать), отсутствие в БД — ошибка.
     *
     * @return array<string, int> "<provider>/<model>" => наименьший
     *                            скорректированный valid_from (unix timestamp)
     */
    private function applyCorrections(PriceListData $data): array
    {
        $affected = [];

        foreach ($data->prices as $row) {
            $provider = $this->providers->findByCode($row['provider'])
                ?? throw new RuntimeException(sprintf('Провайдер "%s" отсутствует в базе.', $row['provider']));
            $model = $this->models->findOne($provider, $row['model'])
                ?? throw new RuntimeException(sprintf('Модель "%s/%s" отсутствует в базе.', $row['provider'], $row['model']));

            $validFrom = UsageEventReader::parseTimestamp($row['valid_from']);

            $priceRow = new PriceRow(
                $provider->toDomain(),
                $model->toDomain(),
                $validFrom,
                PricingUnit::fromString($row['unit']),
                $row['rates'],
                $row['currency'],
            );

            $this->priceRows->upsert($model, $priceRow);

            $qualified = $row['provider'] . '/' . $row['model'];
            $timestamp = (int) $validFrom->format('U');
            $affected[$qualified] = isset($affected[$qualified])
                ? min($affected[$qualified], $timestamp)
                : $timestamp;
        }

        return $affected;
    }

    /**
     * Пересчитывает уже рассчитанные события затронутых моделей.
     *
     * События выбираются по модели и времени >= наиболее раннего
     * скорректированного valid_from: новая строка могла появиться между
     * существующими, и тогда часть событий переключается на неё, формально
     * не ссылаясь на исправленную строку. События, чья стоимость не
     * изменилась, не учитываются, поэтому повторный запуск с тем же файлом
     * даёт REPRICED_EVENTS=0 и дельту 0.
     *
     * @param array<string, int> $affected "<provider>/<model>" => нижняя граница ts
     *
     * @return array{0: int, 1: Decimal}
     */
    private function recalculate(array $affected, string $reason): array
    {
        $repriced = 0;
        $delta = Decimal::zero();

        foreach ($affected as $qualified => $timestamp) {
            [$providerCode, $modelCode] = explode('/', $qualified, 2);

            $provider = $this->providers->findByCode($providerCode);
            $model = null === $provider ? null : $this->models->findOne($provider, $modelCode);
            if (null === $provider || null === $model || null === $model->id()) {
                continue;
            }

            $since = new \DateTimeImmutable('@' . $timestamp);
            $rows = $this->priceRows->findAllForModel($model);
            $domainProvider = $provider->toDomain();

            foreach ($this->events->findRatedForModel($providerCode, $modelCode, $since) as $event) {
                $row = $this->pickPriceRow($rows, $event->occurredAt());
                if (null === $row) {
                    continue;
                }

                $newCost = $this->calculator->costOfRow(
                    $domainProvider,
                    $model->toDomain(),
                    $row->toDomain($domainProvider),
                    $event->occurredAt(),
                    $event->tokens(),
                );

                $oldCost = Decimal::fromString((string) $event->costUsd());
                if ($oldCost->equals($newCost)) {
                    continue;
                }

                $this->adjustments->add(new CostAdjustment(
                    eventId: (int) $event->id(),
                    oldCost: $oldCost,
                    newCost: $newCost,
                    reason: $reason,
                ));

                $event->applyPrice($row->id(), $newCost->fixed(CostCalculator::COST_SCALE));

                ++$repriced;
                $delta = $delta->add($newCost->sub($oldCost));
            }
        }

        return [$repriced, $delta];
    }

    /**
     * @param list<PriceRowEntity> $rows
     */
    private function pickPriceRow(array $rows, \DateTimeImmutable $moment): ?PriceRowEntity
    {
        $best = null;
        foreach ($rows as $row) {
            if ($row->validFrom() > $moment) {
                continue;
            }
            if (null === $best || $row->validFrom() > $best->validFrom()) {
                $best = $row;
            }
        }

        return $best;
    }
}
