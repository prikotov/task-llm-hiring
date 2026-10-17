<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\OffPeakWindow;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Провайдер LLM (справочник) и его оф-пиковая скидка.
 *
 * Оф-пик: окно по времени суток UTC («ЧЧ:ММ», [start; end)) и десятичный
 * множитель. Все три поля null — скидки нет.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public ?int $id = null;

    #[ORM\Column(length: 128)]
    public string $code = '';

    #[ORM\Column(length: 255)]
    public string $name = '';

    /** Время начала окна (UTC), «ЧЧ:ММ»; null — скидки нет. */
    #[ORM\Column(length: 5, nullable: true)]
    public ?string $offPeakStart = null;

    /** Время конца окна (UTC), «ЧЧ:ММ»; null — скидки нет. */
    #[ORM\Column(length: 5, nullable: true)]
    public ?string $offPeakEnd = null;

    /** Множитель стоимости в окне — десятичная строка. */
    #[ORM\Column(length: 40, nullable: true)]
    public ?string $offPeakMultiplier = null;

    /** Оф-пиковое окно провайдера (null — скидки нет). */
    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return OffPeakWindow::parse($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }

    /** Применяет (или снимает) оф-пик — для идемпотентного upsert. */
    public function replaceOffPeak(?OffPeakWindow $offPeak): void
    {
        if ($offPeak === null) {
            $this->offPeakStart = null;
            $this->offPeakEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakStart = \gmdate('H:i', $offPeak->startSeconds());
        $this->offPeakEnd = \gmdate('H:i', $offPeak->endSeconds());
        $this->offPeakMultiplier = $offPeak->multiplier();
    }
}
