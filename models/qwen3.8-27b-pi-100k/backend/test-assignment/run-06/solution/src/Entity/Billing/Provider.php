<?php

declare(strict_types=1);

namespace App\Entity\Billing;

use App\Domain\Billing\OffPeakWindow;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    // Оф-пиковое окно по UTC («HH:MM»); null — скидка отсутствует.
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /** Множитель стоимости в оф-пик — десятичная строка (bcmath), не float. */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name, ?OffPeakWindow $offPeak = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->setOffPeak($offPeak);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setOffPeak(?OffPeakWindow $window): void
    {
        if ($window === null) {
            $this->offPeakWindowStart = null;
            $this->offPeakWindowEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakWindowStart = $window->windowStart;
        $this->offPeakWindowEnd = $window->windowEnd;
        $this->offPeakMultiplier = $window->multiplier;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** Null, если у провайдера нет оф-пиковой скидки. */
    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakWindowStart === null) {
            return null;
        }

        return new OffPeakWindow(
            $this->offPeakWindowStart,
            $this->offPeakWindowEnd ?? '',
            $this->offPeakMultiplier ?? '0',
        );
    }
}
