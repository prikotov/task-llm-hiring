<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Запись стоимости события.
 *
 * - rawCost      — базовая стоимость (масштаб ≥ 16);
 * - appliedCost  — после оф-пика (масштаб ≥ 16);
 * - originalCost — при первом расчёте (округлена до 6 знаков);
 * - currentCost  — действующая стоимость (после reprice).
 *
 * rateKey — привязка к строке цен (provider|model|epoch).
 */
#[ORM\Entity]
#[ORM\Table(name: 'billing_cost_record')]
#[ORM\UniqueConstraint(name: 'uq_billing_cost_record_event', columns: ['event_id'])]
#[ORM\Index(name: 'idx_billing_cost_record_report', columns: ['ts', 'user_id', 'provider', 'model'])]
#[ORM\Index(name: 'idx_billing_cost_record_ratekey', columns: ['rate_key'])]
final class CostRecord
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private int $eventId;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $provider;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $model;

    #[ORM\Column(name: 'user_id', type: Types::STRING, length: 128)]
    private string $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensInput;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensOutput;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheRead;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheWrite;

    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $rateKey;

    #[ORM\Column(columnDefinition: 'numeric(30,16)')]
    private string $rawCost;

    #[ORM\Column(columnDefinition: 'numeric(30,16)')]
    private string $appliedCost;

    #[ORM\Column(columnDefinition: 'numeric(30,6)')]
    private string $originalCost;

    #[ORM\Column(columnDefinition: 'numeric(30,6)')]
    private string $currentCost;

    public function __construct(
        int $eventId,
        string $provider,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        string $rateKey,
        string $rawCost,
        string $appliedCost,
        string $originalCost,
    ) {
        $this->eventId = $eventId;
        $this->provider = $provider;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->rateKey = $rateKey;
        $this->rawCost = $rawCost;
        $this->appliedCost = $appliedCost;
        $this->originalCost = $originalCost;
        $this->currentCost = $originalCost;
    }

    public function id(): ?int { return $this->id; }
    public function eventIdValue(): int { return $this->eventId; }
    public function providerValue(): string { return $this->provider; }
    public function modelValue(): string { return $this->model; }
    public function userValue(): string { return $this->user; }
    public function tsValue(): \DateTimeImmutable { return $this->ts; }
    public function tokensInputValue(): int { return $this->tokensInput; }
    public function tokensOutputValue(): int { return $this->tokensOutput; }
    public function tokensCacheReadValue(): int { return $this->tokensCacheRead; }
    public function tokensCacheWriteValue(): int { return $this->tokensCacheWrite; }
    public function rateKeyValue(): string { return $this->rateKey; }
    public function rawCostValue(): string { return $this->rawCost; }
    public function appliedCostValue(): string { return $this->appliedCost; }
    public function originalCostValue(): string { return $this->originalCost; }
    public function currentCostValue(): string { return $this->currentCost; }

    public function setCurrentCost(string $v): void { $this->currentCost = Money::format($v); }
    public function setRateKey(string $v): void { $this->rateKey = $v; }
}
