<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\CostCalculator;
use App\Domain\EventStatus;
use App\Domain\Model;
use App\Domain\PriceRow;
use App\Domain\Provider;
use App\Domain\UsageEvent;
use App\Support\ImportError;
use App\Support\Money;
use App\Support\PriceFile;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены.
 *
 * Провайдер присылает исправленную строку цены (файл того же формата, секции
 * providers/models могут отсутствовать). Заменённая строка апсёртится по
 * (provider, model, valid_from); события, тарифицированные этой строкой,
 * пересчитываются по новым ставкам. Итоговый отчёт показывает пересчитанные
 * суммы без задвоений.
 */
final class RepriceService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @return array{events: int, delta: string} дельта — новая минус старая сумма (6 знаков) */
    public function reprice(string $path): array
    {
        $data = PriceFile::parse($path);

        $affectedRowIds = [];
        $this->em->wrapInTransaction(function () use ($data, &$affectedRowIds): void {
            foreach ($data['prices'] as $p) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $p['provider']]);
                if ($provider === null) {
                    throw new ImportError(sprintf('reprice "%s/%s": провайдер "%s" отсутствует в справочнике', $p['provider'], $p['model'], $p['provider']));
                }
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $p['model']]);
                if ($model === null) {
                    throw new ImportError(sprintf('reprice "%s/%s": модель отсутствует в справочнике', $p['provider'], $p['model']));
                }

                $row = $this->em->getRepository(PriceRow::class)->findOneBy([
                    'provider' => $provider,
                    'model' => $model,
                    'validFrom' => $p['valid_from'],
                ]);
                if ($row === null) {
                    $row = PriceRow::make($provider, $model, $p['valid_from']);
                    $this->em->persist($row);
                }
                $row->update($p['valid_from'], $p['currency'], $p['unit'], $p['rates']);
                $affectedRowIds[] = $row->getId() ?? 0;

                if ($row->getId() === null) {
                    $this->em->flush();
                }
            }
        });

        // Пересчёт событий, рассчитанных заменёнными строками.
        $rowIds = array_values(array_unique(array_filter($affectedRowIds, static fn (int $id): bool => $id > 0)));
        $oldSum = '0';
        $newSum = '0';
        $count = 0;

        if ($rowIds !== []) {
            /** @var UsageEvent[] $events */
            $events = $this->em->createQuery(
                'SELECT e FROM App\Domain\UsageEvent e WHERE e.status = :status AND e.priceRow IN (:rows)',
            )
                ->setParameter('status', EventStatus::Rated)
                ->setParameter('rows', $rowIds)
                ->getResult();

            foreach ($events as $event) {
                $row = $event->getPriceRow();
                if ($row === null) {
                    continue;
                }
                $oldSum = Money::add($oldSum, (string) $event->getCostUsd());
                $newCost = CostCalculator::calculate($row, $event);
                $newSum = Money::add($newSum, $newCost);
                $event->markRated($newCost, $row);
                $count++;
            }
            $this->em->flush();
        }

        return [
            'events' => $count,
            'delta' => Money::format(Money::sub($newSum, $oldSum), 6),
        ];
    }
}
