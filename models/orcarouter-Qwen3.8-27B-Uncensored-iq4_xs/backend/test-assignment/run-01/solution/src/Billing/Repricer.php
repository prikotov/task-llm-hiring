<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use App\Import\PricesFile;
use App\Util\MoneyCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Применяет исправленные строки цен (upsert по (provider, model, valid_from)
 * по данным уже загруженного справочника: провайдер и модель обязаны
 * существовать в базе), после чего пересчитывает именно события,
 * тарифицировавшиеся заменёнными строками (price_row_id + status = rated).
 *
 * Механизм: перезапись стоимости событий той же строкой без
 * компенсации; ссылка price_row не меняется (это та же строка,
 * изменённая in place), поэтому дубли в отчёте невозможны.
 */
final class Repricer
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return array{events: int, delta: string} delta — новая минус старая
     *         сумма, десятичная строка с 6 знаками (может быть отрицательной)
     */
    public function reprice(PricesFile $file): array
    {
        $em = $this->em;

        return $em->wrapInTransaction(function (EntityManagerInterface $em) use ($file) {
            $providers = $em->getRepository(Provider::class);
            $models = $em->getRepository(Model::class);
            $priceRows = $em->getRepository(PriceRow::class);

            $count = 0;
            $delta = '0';

            foreach ($file->prices() as $p) {
                $ctx = sprintf('provider="%s" model="%s" valid_from', $p['provider'], $p['model']);

                $provider = $providers->findOneBy(['code' => $p['provider']]);
                if ($provider === null) {
                    throw new ImportException(sprintf(
                        'Ретро-коррекция %s: не найден провайдер "%s" в базе',
                        $ctx,
                        $p['provider'],
                    ));
                }
                $model = $models->findOneBy(['provider' => $provider, 'code' => $p['model']]);
                if ($model === null) {
                    throw new ImportException(sprintf(
                        'Ретро-коррекция %s: не найдена модель "%s" в базе',
                        $ctx,
                        $p['model'],
                    ));
                }

                $row = $priceRows->findOneBy([
                    'provider' => $provider,
                    'model' => $model,
                    'validFrom' => $p['valid_from'],
                ]);

                if ($row === null) {
                    // Строки такого ключа не было — пересчитывать нечего.
                    $em->persist(new PriceRow(
                        $provider,
                        $model,
                        $p['valid_from'],
                        $p['currency'],
                        $p['unit'],
                        $p['rates'],
                    ));
                } else {
                    // События, рассчитанные именно этой строкой (до изменения данных).
                    $events = $em->getRepository(UsageEvent::class)->findBy([
                        'priceRow' => $row,
                        'status' => UsageEvent::STATUS_RATED,
                    ]);

                    $row->apply($p['currency'], $p['unit'], $p['rates']);

                    $offPeak = $provider->offPeak();
                    foreach ($events as $event) {
                        $newCost = CostCalculator::computeEventCost(
                            $event->tokens(),
                            $row,
                            $offPeak,
                            $event->getTs(),
                        );

                        // В итоговую статистику попадают только реальные изменения,
                        // чтобы повторный запуск той же правки дал 0 событий.
                        if (MoneyCalculator::eq($newCost, (string) $event->getCostUsd())) {
                            continue;
                        }

                        $count++;
                        $delta = MoneyCalculator::add($delta, MoneyCalculator::sub($newCost, (string) $event->getCostUsd()));
                        $event->setCostUsd($newCost);
                    }
                }

                $em->flush(); // фиксация: повторные строки в одном файле должны находить уже созданную строку
            }

            $em->flush();

            return ['events' => $count, 'delta' => MoneyCalculator::normalize($delta)];
        });
    }
}
