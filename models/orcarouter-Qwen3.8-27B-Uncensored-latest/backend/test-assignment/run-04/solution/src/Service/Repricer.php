<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Pricing\EventRater;
use App\Domain\Pricing\PriceEntry;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Import\PriceData;
use App\Shared\ImportException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Bonus: retroactive price correction (app:billing:reprice).
 *
 * Applies corrected price rows (upsert by provider+model+valid_from against
 * the loaded reference data) and silently re-prices every rated event that
 * was priced with a replaced row. The report keeps showing each event once,
 * with the corrected cost; already shown costs change through this command.
 */
final class Repricer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Connection $db,
        private readonly PriceCatalogImporter $catalogImporter,
        private readonly EventRater $rater,
    ) {
    }

    /**
     * @param list<PriceData> $prices parsed correction rows
     *
     * @return array{events: int, delta: string}
     */
    public function reprice(array $prices): array
    {
        foreach ($prices as $price) {
            $this->assertCatalogRowExists($price);
        }

        $result = $this->db->transactional(function () use ($prices): array {
            $rowIds = [];

            foreach ($prices as $price) {
                $rowIds[] = (int) $this->db->fetchOne(
                    $this->upsertSql(),
                    [
                        'provider' => $price->provider,
                        'model' => $price->model,
                        'valid_from' => $price->validFrom->format('Y-m-d H:i:sO'),
                        'currency' => $price->currency,
                        'unit' => $price->unit,
                        'ri' => $price->rates['input'] ?? null,
                        'ro' => $price->rates['output'] ?? null,
                        'rc' => $price->rates['cache_read'] ?? null,
                        'rw' => $price->rates['cache_write'] ?? null,
                        'ra' => $price->rates['any'] ?? null,
                    ],
                );
            }

            $this->em->clear();

            $events = $this->em->createQuery(
                'SELECT e FROM App\Entity\UsageEvent e
                 WHERE e.status = :rated AND e.priceRowId IN (:rowIds)',
            )
                ->setParameter('rated', UsageEvent::STATUS_RATED)
                ->setParameter('rowIds', $rowIds, ArrayParameterType::INTEGER)
                ->getResult();

            $offPeaks = [];
            $rowEntries = [];
            foreach ($rowIds as $rowId) {
                $row = $this->em->find(PriceRow::class, $rowId);
                $rowEntries[$rowId] = $row?->toPriceEntry() ?? new PriceEntry('invalid', []);
            }

            $delta = '0';
            $count = 0;
            foreach ($events as $event) {
                $event = $event instanceof UsageEvent ? $event : $event[0];
                /** @var UsageEvent $event */

                $newCost = $this->rater->rate(
                    $event->toUsageRecord(),
                    $rowEntries[(int) $event->getPriceRowId()],
                    $this->offPeakOf($event->getProvider(), $offPeaks),
                );

                $delta = \bcadd($delta, \bcsub($newCost, (string) $event->getCost(), 16), 6);
                $event->markRated((int) $event->getPriceRowId(), $newCost);
                ++$count;
            }

            $this->em->flush();

            return ['events' => $count, 'delta' => $delta];
        });

        return $result;
    }

    private function assertCatalogRowExists(PriceData $price): void
    {
        $providerExists = (int) $this->db->fetchOne('SELECT 1 FROM providers WHERE code = :code', ['code' => $price->provider]) === 1;
        $modelExists = (int) $this->db->fetchOne(
            'SELECT 1 FROM models WHERE provider = :provider AND code = :code',
            ['provider' => $price->provider, 'code' => $price->model],
        ) === 1;

        if (!$providerExists || !$modelExists) {
            throw ImportException::forFile(
                '<reprice>',
                \sprintf('correction for %s/%s rejected: provider or model is not in the loaded catalog', $price->provider, $price->model),
            );
        }
    }

    private function offPeakOf(string $code, array &$cache): ?\App\Domain\Pricing\OffPeak
    {
        $cache[$code] ??= $this->em->find(Provider::class, $code)?->offPeak();

        return $cache[$code];
    }

    private function upsertSql(): string
    {
        return 'INSERT INTO price_rows (provider, model, valid_from, currency, unit, rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any)
                VALUES (:provider, :model, :valid_from, :currency, :unit, :ri, :ro, :rc, :rw, :ra)
                ON CONFLICT (provider, model, valid_from) DO UPDATE SET
                    currency = EXCLUDED.currency,
                    unit = EXCLUDED.unit,
                    rate_input = EXCLUDED.rate_input,
                    rate_output = EXCLUDED.rate_output,
                    rate_cache_read = EXCLUDED.rate_cache_read,
                    rate_cache_write = EXCLUDED.rate_cache_write,
                    rate_any = EXCLUDED.rate_any
                RETURNING id';
    }
}
