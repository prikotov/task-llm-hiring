<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\PriceRow;
use App\Domain\Model\ModelEntity;
use App\Domain\Model\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника: провайдеры (по code), модели (по
 * (provider, code)), строки цен (по (provider, model, valid_from)).
 *
 * Повторная загрузка тех же данных не изменяет их.
 */
final class PriceBookImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param array $data распарсенный JSON истории цен
     *
     * @throws \InvalidArgumentException при некорректных данных
     */
    public function import(array $data): void
    {
        $em = $this->em;
        $em->beginTransaction();
        try {
            $this->upsertProviders($data['providers'] ?? []);
            $this->upsertModels($data['models'] ?? []);
            $this->upsertPrices($data['prices'] ?? []);
            $em->flush();
            $em->commit();
        } catch (\Throwable $e) {
            $em->rollBack();
            throw $e;
        }
    }

    /** @param list<array> $rows */
    private function upsertProviders(array $rows): void
    {
        /** @var Provider[] $repo */
        $repo = $this->em->getRepository(Provider::class);
        foreach ($rows as $row) {
            if (empty($row['code']) || empty($row['name'])) {
                throw new \InvalidArgumentException('Provider: code и name обязательны');
            }
            $existing = $repo->findBy(['code' => $row['code']]);
            $p = $existing[0] ?? null;
            if (null === $p) {
                $p = new Provider($row['code'], (string) $row['name'], null, null, null);
                $this->em->persist($p);
            }
            $this->applyOffPeak($p, (($row['off_peak'] ?? null) ?? []));
        }
    }

    /** @param list<array> $rows */
    private function upsertModels(array $rows): void
    {
        /** @var ModelEntity[] $repo */
        $repo = $this->em->getRepository(ModelEntity::class);
        foreach ($rows as $row) {
            if (empty($row['provider']) || empty($row['code'])) {
                throw new \InvalidArgumentException('Model: provider и code обязательны');
            }
            $existing = $repo->findBy(['providerCode' => $row['provider'], 'code' => $row['code']]);
            $m = $existing[0] ?? null;
            if (null === $m) {
                $m = new ModelEntity($row['provider'], (string) $row['code'], (string) $row['code']);
                $this->em->persist($m);
            }
        }
    }

    /** @param list<array> $rows */
    private function upsertPrices(array $rows): void
    {
        foreach ($rows as $row) {
            if (empty($row['provider']) || empty($row['model']) || empty($row['valid_from'])) {
                throw new \InvalidArgumentException('PriceRow: provider, model, valid_from обязательны');
            }
            if (empty($row['unit'])) {
                throw new \InvalidArgumentException('PriceRow: unit обязателен');
            }
            if (!is_array($row['rates'] ?? null) || [] === $row['rates']) {
                throw new \InvalidArgumentException('PriceRow: rates — непустой объект');
            }

            $existing = $this->priceRowExists((string) $row['provider'], (string) $row['model'], (string) $row['valid_from']);
            if ($existing) {
                continue; // та же строка уже есть — не меняем
            }

            $this->em->persist(new PriceRow(
                (string) $row['provider'],
                (string) $row['model'],
                new \DateTimeImmutable($row['valid_from']),
                (string) ($row['currency'] ?? 'USD'),
                (string) $row['unit'],
                $row['rates'],
            ));
        }
    }

    private function priceRowExists(string $provider, string $model, string $validFrom): bool
    {
        $row = $this->em->createQueryBuilder()
            ->select('p')
            ->from(PriceRow::class, 'p')
            ->where('p.providerCode = :p')
            ->andWhere('p.modelCode = :m')
            ->andWhere('p.validFrom = :v')
            ->setParameter('p', $provider)
            ->setParameter('m', $model)
            ->setParameter('v', new \DateTimeImmutable($validFrom))
            ->getSingleResultOrNull();

        return null !== $row;
    }

    /** @param array $offPeak */
    private function applyOffPeak(Provider $p, array $offPeak): void
    {
        if (empty($offPeak)) {
            $p->windowStart = null;
            $p->windowEnd = null;
            $p->offPeakMultiplier = null;

            return;
        }
        $p->windowStart = $offPeak['window_start'] ?? null;
        $p->windowEnd = $offPeak['window_end'] ?? null;
        $p->offPeakMultiplier = $offPeak['multiplier'] ?? null;
    }
}
