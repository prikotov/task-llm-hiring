<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine\Entity;

use App\Billing\Domain\Model\OffPeakWindow;
use App\Billing\Domain\Model\Provider;
use App\Billing\Domain\Model\TimeOfDay;
use App\Billing\Domain\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Персистентная запись справочника провайдеров.
 *
 * Отдельная от доменного Provider сущность: домен не знает про Doctrine
 * (никаких атрибутов маппинга в Domain/). Преобразование — в маппере.
 */
#[ORM\Entity]
#[ORM\Table(name: 'provider')]
#[ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
final class ProviderRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 64)]
    private string $code;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start_minute', type: Types::SMALLINT, nullable: true)]
    private ?int $offPeakStartMinute = null;

    #[ORM\Column(name: 'off_peak_end_minute', type: Types::SMALLINT, nullable: true)]
    private ?int $offPeakEndMinute = null;

    /**
     * Ставка множителя — decimal-строка; float не используется даже для
     * "не денежного" множителя, чтобы арифметика оставалась точной.
     */
    #[ORM\Column(name: 'off_peak_multiplier', type: Types::DECIMAL, precision: 30, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    /**
     * @var Collection<int, ModelRecord>
     */
    #[ORM\OneToMany(targetEntity: ModelRecord::class, mappedBy: 'provider')]
    private Collection $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

    public static function fromDomain(Provider $provider): self
    {
        $record = new self();
        $record->apply($provider);

        return $record;
    }

    public function apply(Provider $provider): void
    {
        $this->code = $provider->code;
        $this->name = $provider->name;
        $this->offPeakStartMinute = $provider->offPeak?->start->secondsSinceMidnight();
        $this->offPeakEndMinute = $provider->offPeak?->end->secondsSinceMidnight();
        $this->offPeakMultiplier = $provider->offPeak?->multiplier->toString();
    }

    public function toDomain(): Provider
    {
        $offPeak = null;

        if (null !== $this->offPeakStartMinute && null !== $this->offPeakEndMinute && null !== $this->offPeakMultiplier) {
            $offPeak = new OffPeakWindow(
                TimeOfDay::fromSecondsSinceMidnight($this->offPeakStartMinute),
                TimeOfDay::fromSecondsSinceMidnight($this->offPeakEndMinute),
                Money::fromString($this->offPeakMultiplier),
            );
        }

        return new Provider($this->code, $this->name, $offPeak);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }
}
