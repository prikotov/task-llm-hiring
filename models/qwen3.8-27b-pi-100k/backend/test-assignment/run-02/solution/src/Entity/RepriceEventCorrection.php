<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал корректировок на уровне событий: что было до и после
 * (строка цены, её условия — схема и ставки — и стоимость).
 *
 * Журнал самодостаточен: снимок условий сохранён, даже если строка цен
 * была перезаписана на месте.
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_event_corrections')]
final class RepriceEventCorrection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RepriceRun::class)]
    #[ORM\JoinColumn(name: 'run_id', referencedColumnName: 'id', nullable: false)]
    private RepriceRun $run;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    private UsageEvent $event;

    /** Строка цены «до» (если событие по ней тарифицировалось). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'old_price_row_id', referencedColumnName: 'id', nullable: true)]
    private ?PriceRow $oldPriceRow = null;

    /** Строка цены «после». */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'new_price_row_id', referencedColumnName: 'id', nullable: false)]
    private PriceRow $newPriceRow;

    /** Снимок условий «до» (schema + ставки), USD 6 знаков. */
    #[ORM\Column(length: 32)]
    private string $oldUnit;

    /** @var array<string, string> */
    #[ORM\Column(type: Types::JSON)]
    private array $oldRates;

    /** @var array<string, string> */
    #[ORM\Column(type: Types::JSON)]
    private array $newRates;

    #[ORM\Column(type: Types::DECIMAL, precision: 38, scale: 6)]
    private string $oldCostUsd;

    #[ORM\Column(type: Types::DECIMAL, precision: 38, scale: 6)]
    private string $newCostUsd;

    public function __construct(
        RepriceRun $run,
        UsageEvent $event,
        ?PriceRow $oldPriceRow,
        PriceRow $newPriceRow,
        string $oldUnit,
        array $oldRates,
        array $newRates,
        string $oldCostUsd,
        string $newCostUsd,
    ) {
        $this->run = $run;
        $this->event = $event;
        $this->oldPriceRow = $oldPriceRow;
        $this->newPriceRow = $newPriceRow;
        $this->oldUnit = $oldUnit;
        $this->oldRates = $oldRates;
        $this->newRates = $newRates;
        $this->oldCostUsd = $oldCostUsd;
        $this->newCostUsd = $newCostUsd;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRun(): RepriceRun
    {
        return $this->run;
    }

    public function getEvent(): UsageEvent
    {
        return $this->event;
    }

    public function getOldCostUsd(): string
    {
        return $this->oldCostUsd;
    }

    public function getNewCostUsd(): string
    {
        return $this->newCostUsd;
    }
}
