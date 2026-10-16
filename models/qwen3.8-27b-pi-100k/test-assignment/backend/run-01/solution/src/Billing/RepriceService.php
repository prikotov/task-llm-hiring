<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Import\PriceFile;
use App\Import\PriceImporter;
use App\Support\Decimals;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Ретро-коррекция (бонус ТЗ): применить исправленные строки цен и
 * пересчитать события, которые тарифицировались заменёнными строками.
 * Уже показанные пользователю расходы корректняются «местом»:
 * стоимость события перезаписывается, дельта — новая минус старая.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CostCalculator $calculator,
        private readonly PriceImporter $importer,
    ) {
    }

    /** @return array{events: int, delta: string} */
    public function reprice(PriceFile $file): array
    {
        $affectedRowIds = [];
        $delta = '0';
        $events = 0;

        $this->em->getConnection()->transactional(
            function () use ($file, &$affectedRowIds, &$delta, &$events): void {
                // 1) до замены — найти строки, чьё содержимое реально изменится
                foreach ($file->getPrices() as $spec) {
                    $model = $this->findModel($spec->getProviderCode(), $spec->getModel());
                    if ($model === null) {
                        continue; // импорт ниже бросит понятную ошибку
                    }
                    $row = $this->findRow($model->getId(), $spec->getValidFrom());
                    if ($row === null) {
                        continue; // новая строка — старые события к ней не привязаны
                    }
                    if (!$spec->sameContent([
                        'unit' => $row->getUnit(),
                        'currency' => $row->getCurrency(),
                        'rates' => $row->getRates(),
                    ])) {
                        $affectedRowIds[] = $row->getId();
                    }
                }

                // 2) применить файл (upsert провайдеров/моделей/цены)
                $this->importer->import($file);

                // 3) пересчёт rated-событий по заменённым строкам
                foreach (array_unique($affectedRowIds) as $rowId) {
                    /** @var UsageEvent[] $affected */
                    $affected = $this->em->getRepository(UsageEvent::class)->findBy([
                        'priceRowId' => $rowId,
                        'status' => UsageEvent::STATUS_RATED,
                    ]);

                    $row = $this->em->find(PriceRow::class, $rowId);
                    if ($row === null) {
                        continue;
                    }

                    foreach ($affected as $event) {
                        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $event->getProvider()]);
                        $result = $this->calculator->calculate(
                            $row,
                            $event->getTokens(),
                            $provider?->getOffPeak() ?? null,
                            $event->getTs(),
                        );
                        $delta = Decimals::add($delta, Decimals::sub($result['costUsd'], $event->getCostUsd() ?? '0'));
                        $event->markRated($rowId, $result['baseCost'], $result['offPeakApplied'], $result['costUsd']);
                        $events++;
                    }
                }

                $this->em->flush();
            },
        );

        return ['events' => $events, 'delta' => $delta];
    }

    private function findModel(string $providerCode, string $model): ?\App\Entity\Model
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(\App\Entity\Model::class, 'm')
            ->innerJoin(Provider::class, 'p', 'WITH', 'p.id = m.providerId')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $model)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function findRow(int $modelId, \DateTimeImmutable $validFrom): ?PriceRow
    {
        $row = $this->em->getRepository(PriceRow::class)->findOneBy([
            'modelId' => $modelId,
            'validFrom' => $validFrom,
        ]);

        return $row;
    }
}
