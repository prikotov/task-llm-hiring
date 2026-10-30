<?php

declare(strict_types=1);

namespace App\Application\Billing\Reprice;

use App\Application\Ingestion\PriceCatalogInput;
use App\Application\Ingestion\PriceCatalogParser;
use App\Domain\Billing\BillingService;
use App\Domain\Billing\Cost;
use App\Domain\Billing\PriceCatalog;
use App\Domain\DomainException;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\Provider;
use App\Domain\Pricing\StoredPriceRow;
use App\Domain\Usage\RatedEventEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Бонус: ретро-коррекция цены (app:billing:reprice).
 *
 * 1. Импортирует исправленные строки цен — upsert по (provider, model,
 *    valid_from). Провайдер и модель должны существовать в базе; секции
 *    `providers`/`models` в файле коррекции могут отсутствовать или быть
 *    пустыми.
 *
 * 2. Пересчитывает уже рассчитанные события, которые тарифицировались
 *    заменёнными строками (то есть события, чья модель и провайдер входят
 *    в затронутые пары, И чья timestamp попадает в окно корректной строки).
 *
 * Вывод: две строки в stdout:
 *   REPRICED_EVENTS=<n>
 *   REPRICE_DELTA_USD=<x.xxxxxx>
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingService $billing,
    ) {
    }

    /**
     * @throws DomainException
     * @return array{events:int, delta:Cost}
     */
    public function reprice(string $jsonFile): array
    {
        $content = file_get_contents($jsonFile);
        if ($content === false) {
            throw new DomainException("cannot read file: {$jsonFile}");
        }
        $parser = new PriceCatalogParser();
        try {
            $input = $parser->parse($content, "RepriceService::reprice ({$jsonFile})");
        } catch (\Throwable $e) {
            throw DomainException::wrap($e, "RepriceService::reprice ({$jsonFile})");
        }

        $em = $this->em;
        $affectedPairs = $em->wrapInTransaction(function () use ($input, $em): array {
            $this->upsertPriceRows($input, $em);
            $pairs = [];
            foreach ($input->priceRows as $row) {
                $pairs[$row->provider . '/' . $row->model] = true;
            }

            return $pairs;
        });

        $delta = Cost::zero();
        $recount = 0;

        foreach ($this->em->getRepository(RatedEventEntity::class)->findAll() as $e) {
            /** @var RatedEventEntity $e */
            if (!isset($affectedPairs[$e->provider . '/' . $e->model])) {
                continue;
            }

            $usage = $e->toDomain();
            $oldCost = $e->cost();
            $newRow = $this->findApplicablePriceRow($usage->provider, $usage->model, $usage->ts);

            if ($newRow === null) {
                // строка была удалена из справочника (закрепляем в допущениях PR_DESCRIPTION.md)
                continue;
            }

            $newCost = $this->billing->calculateCost($usage, $newRow);
            if ($newCost->toSixDecimalsTruncated() !== $oldCost->toSixDecimalsTruncated()) {
                $e->costUsd = $newCost->toSixDecimalsTruncated();
                $e->repricedCount = $e->repricedCount + 1;
                $delta = $delta->add($newCost)->subtract($oldCost);
                $recount++;
            }
        }

        $this->em->flush();

        return ['events' => $recount, 'delta' => $delta];
    }

    private function upsertPriceRows(PriceCatalogInput $input, EntityManagerInterface $em): void
    {
        foreach ($input->priceRows as $row) {
            $existing = $em->getRepository(StoredPriceRow::class)->findOneBy([
                'provider' => $row->provider,
                'model' => $row->model,
                'validFrom' => $row->validFrom,
            ]);

            $domain = $row->toDomain();

            if ($existing !== null) {
                StoredPriceRow::applyTo(
                    $existing,
                    provider: $row->provider,
                    model: $row->model,
                    validFrom: $row->validFromDateTime(),
                    currency: $row->currency,
                    unit: $domain->unit,
                    rates: $domain->rates,
                );
                continue;
            }

            // новая строка: проверяем, что провайдер и модель существуют в базе
            $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $row->provider]);
            if ($provider === null) {
                throw new DomainException(
                    "reprice: unknown provider \"{$row->provider}\" (not in providers table)"
                );
            }
            $model = $em->getRepository(\App\Domain\Pricing\Model::class)->findOneBy(
                ['provider' => $row->provider, 'code' => $row->model]
            );
            if ($model === null) {
                throw new DomainException(
                    "reprice: unknown model \"{$row->model}\" of provider \"{$row->provider}\""
                );
            }

            $new = StoredPriceRow::create(
                provider: $row->provider,
                model: $row->model,
                validFrom: $row->validFromDateTime(),
                currency: $row->currency,
                unit: $domain->unit,
                rates: $domain->rates,
            );
            $em->persist($new);
        }
    }

    /**
     * Строка цен модели, действующая на момент $ts (max valid_from ≤ ts),
     * либо null — если строки нет вообще (например, была удалена).
     */
    private function findApplicablePriceRow(
        string $provider,
        string $model,
        \DateTimeImmutable $ts,
    ): ?PriceRow {
        $row = $this->em
            ->createQueryBuilder()
            ->select('p')
            ->from(StoredPriceRow::class, 'p')
            ->where('p.provider = :prov')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('prov', $provider)
            ->setParameter('model', $model)
            ->setParameter('ts', $ts->format('Y-m-d\TH:i:s\\Z'), \Doctrine\DBAL\Types\Types::STRING)
            ->getQuery()
            ->getOneOrNullResult();

        if ($row === null) {
            return null;
        }

        return $row->toDomain();
    }
}
