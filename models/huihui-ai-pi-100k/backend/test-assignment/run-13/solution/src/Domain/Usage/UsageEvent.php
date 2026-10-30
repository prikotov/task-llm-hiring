<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Money\Cost;
use App\Domain\Model\Model;
use App\Domain\Provider\Provider;
use App\Domain\Price\PriceRow;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'usage_events',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_usage_event', columns: ['provider', 'provider_request_id']),
    ],
)]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'provider_code', referencedColumnName: 'code', onDelete: 'CASCADE')]
    private Provider $provider;

    #[ORM\Column(type: 'string')]
    private string $providerRequestId;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Model $model;

    #[ORM\Column(type: 'string')]
    private string $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite;

    /** null — unrated (цена ещё не выбрана). */
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?PriceRow $priceRow = null;

    /** Округлённая итоговая стоимость события (6 знаков) — или null, если unrated. */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $costUsd = null;

    public function __construct(
        Provider $provider,
        string $providerRequestId,
        Model $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function providerRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function ts(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function tokensInput(): int
    {
        return $this->tokensInput;
    }

    public function tokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function tokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function tokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function priceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function costUsd(): ?Cost
    {
        return $this->costUsd === null ? null : Cost::fromString($this->costUsd);
    }

    public function isRated(): bool
    {
        return $this->priceRow !== null && $this->costUsd !== null;
    }

    public function assignRate(PriceRow $priceRow, Cost $eventCost): void
    {
        $this->priceRow = $priceRow;
        $this->costUsd = $eventCost->toFixedString(6);
    }
}
