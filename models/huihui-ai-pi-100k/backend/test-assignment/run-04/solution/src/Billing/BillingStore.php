<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Ingest\Defs\UsageEventDef;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Работа с БД: выбор применимой строки цены и обновление событий.
 * Точную арифметику выполняет Domain\CostCalculator.
 */
final class BillingStore
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Строка цены, применимая к моменту $tsUtc: наибольший valid_from ≤ tsUtc
     * (valid_from включительно).
     */
    public function findLatestPrice(string $providerCode, string $modelCode, \DateTimeImmutable $tsUtc): ?PriceRow
    {
        $row = $this->em->createQuery(
            'SELECT p FROM App\Entity\PriceRow p
             WHERE p.providerCode = :p AND p.modelCode = :m AND p.validFrom <= :ts
             ORDER BY p.validFrom DESC, p.id DESC'
        )
            ->setParameters(['p' => $providerCode, 'm' => $modelCode, 'ts' => $tsUtc])
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return $row instanceof PriceRow ? $row : null;
    }

    /** Все строки цен модели (для reprice-подбора применимой строки). */
    /** @return PriceRow[] */
    public function findPriceRows(string $providerCode, string $modelCode): array
    {
        return $this->em->createQuery(
            'SELECT p FROM App\Entity\PriceRow p
             WHERE p.providerCode = :p AND p.modelCode = :m
             ORDER BY p.validFrom ASC, p.id ASC'
        )
            ->setParameters(['p' => $providerCode, 'm' => $modelCode])
            ->getResult();
    }

    public function findProvider(string $code): ?Provider
    {
        return $this->em->getRepository(Provider::class)->find($code);
    }

    /** @return UsageEvent[] */
    public function findUnratedEvents(): array
    {
        return $this->em->createQuery(
            'SELECT e FROM App\Entity\UsageEvent e WHERE e.status = :s ORDER BY e.id ASC'
        )
            ->setParameter('s', UsageEvent::STATUS_UNRATED)
            ->getResult();
    }

    public function markRated(UsageEvent $event, string $cost, int $priceRowId): void
    {
        $event->status = UsageEvent::STATUS_RATED;
        $event->cost = $cost;
        $event->priceRowId = $priceRowId;
    }

    public function setCost(UsageEvent $event, string $cost): void
    {
        $event->cost = $cost;
    }

    /**
     * Идемпотентная вставка события: при дубле (provider, provider_request_id) запись не создаётся.
     */
    public function persistImported(UsageEventDef $def): void
    {
        $conn = $this->em->getConnection();
        $exists = $conn->fetchOne(
            'SELECT 1 FROM usage_events WHERE provider_code = ? AND provider_request_id = ?',
            [$def->providerCode, $def->providerRequestId]
        );

        if ($exists !== false) {
            return; // дубль — игнорируем целиком
        }

        $conn->insert('usage_events', [
            'provider_code' => $def->providerCode,
            'provider_request_id' => $def->providerRequestId,
            'model_code' => $def->modelCode,
            'user_id' => $def->user,
            'ts' => $def->tsUtc->format('Y-m-d H:i:s'),
            'tokens_input' => $def->tokensInput,
            'tokens_output' => $def->tokensOutput,
            'tokens_cache_read' => $def->tokensCacheRead,
            'tokens_cache_write' => $def->tokensCacheWrite,
            'status' => UsageEvent::STATUS_UNRATED,
            'cost_usd' => null,
            'price_row_id' => null,
        ]);
    }
}
