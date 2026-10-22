<?php

declare(strict_types=1);

namespace App\Domain\Provider;

use App\Domain\Model\Model;
use App\Domain\Model\PriceLine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider')]
#[ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    /** @var string|null "HH:MM" (UTC), включительно */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    /** @var string|null "HH:MM" (UTC), исключительно */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /** @var string|null десятичный множитель стоимости (например "0.5") */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    /** @var Collection<int, PriceLine> */
    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: PriceLine::class)]
    private Collection $priceLines;

    /** @var Collection<int, Model> */
    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Model::class)]
    private Collection $models;

    public function __construct()
    {
        $this->priceLines = new ArrayCollection();
        $this->models = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): self
    {
        $this->offPeakWindowStart = $windowStart;
        $this->offPeakWindowEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;

        return $this;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /**
     * Проверка попадает ли время суток (UTC) в оф-пиковое окно.
     *
     * Окно [start, end); поддерживается переход через полночь
     * (start > end, например "16:30"–"00:30").
     *
     * @param int $minutesOfDay минуты с начала суток UTC (0..1439)
     */
    public function isOffPeak(int $minutesOfDay): bool
    {
        if ($this->offPeakWindowStart === null || $this->offPeakWindowEnd === null) {
            return false;
        }

        $start = self::toMinutes($this->offPeakWindowStart);
        $end = self::toMinutes($this->offPeakWindowEnd);

        if ($start <= $end) {
            return $minutesOfDay >= $start && $minutesOfDay < $end;
        }

        // окно пересекает полночь
        return $minutesOfDay >= $start || $minutesOfDay < $end;
    }

    public static function toMinutes(string $hhmm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhmm));

        return $h * 60 + $m;
    }

    /** @return Collection<int, PriceLine> */
    public function getPriceLines(): Collection
    {
        return $this->priceLines;
    }

    /** @return Collection<int, Model> */
    public function getModels(): Collection
    {
        return $this->models;
    }
}
