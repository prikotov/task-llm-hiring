<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал запусков app:billing:reprice — одна запись на команду.
 *
 * Аудит: какие строки цен были применены из коррекции, сколько событий
 * пересчитано и какова суммарная дельта (новая сумма минус старая).
 * Пересчитанные сами события перечислены в RepriceEventCorrection.
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_runs')]
final class RepriceRun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Имя файла коррекции, применённой этим запуском. */
    #[ORM\Column(length: 512)]
    private string $sourceFile;

    /**
     * Строки цен, затронутые коррекцией (provider/model@valid_from).
     *
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $affectedPriceKeys;

    /** Число событий, чья стоимость изменилась. */
    #[ORM\Column(type: Types::INTEGER)]
    private int $eventsRepriced;

    /** Новая сумма цен пересчитанных событий минус старая (USD, 6 знаков). */
    #[ORM\Column(type: Types::DECIMAL, precision: 38, scale: 6)]
    private string $deltaUsd;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $appliedAt;

    #[ORM\OneToMany(mappedBy: 'run', targetEntity: RepriceEventCorrection::class)]
    private \Doctrine\Common\Collections\Collection $corrections;

    public function __construct(
        string $sourceFile,
        array $affectedPriceKeys,
        int $eventsRepriced,
        string $deltaUsd,
        \DateTimeImmutable $appliedAt,
    ) {
        $this->sourceFile = $sourceFile;
        $this->affectedPriceKeys = $affectedPriceKeys;
        $this->eventsRepriced = $eventsRepriced;
        $this->deltaUsd = $deltaUsd;
        $this->appliedAt = $appliedAt;
        $this->corrections = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceFile(): string
    {
        return $this->sourceFile;
    }

    /** @return list<string> */
    public function getAffectedPriceKeys(): array
    {
        return $this->affectedPriceKeys;
    }

    public function getEventsRepriced(): int
    {
        return $this->eventsRepriced;
    }

    public function getDeltaUsd(): string
    {
        return $this->deltaUsd;
    }

    public function getAppliedAt(): \DateTimeImmutable
    {
        return $this->appliedAt;
    }
}
