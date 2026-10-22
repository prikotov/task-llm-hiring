<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_provider')]
class Provider
{
    public function __construct(
        #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
        private ?int $id,

        // Естественный ключ провайдера.
        #[ORM\Column(type: 'string', length: 64, unique: true)]
        private string $code = '',

        #[ORM\Column(type: 'string', length: 255)]
        private string $name = '',

        // Оф-пиковая скидка: окно [window_start, window_end) по UTC, может через полночь.
        #[ORM\Column(type: 'string', length: 5, nullable: true)]
        private ?string $offPeakWindowStart = null,

        #[ORM\Column(type: 'string', length: 5, nullable: true)]
        private ?string $offPeakWindowEnd = null,

        // Множитель стоимости события, попавшего в окно (десятичная строка).
        #[ORM\Column(type: 'string', length: 32, nullable: true)]
        private ?string $offPeakMultiplier = null,
    ) {
    }

    public static function make(string $code, string $name): self
    {
        return new self(id: null, code: $code, name: $name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Загружает параметры окна оф-пика; null — скидка снята.
     */
    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): void
    {
        $this->offPeakWindowStart = $windowStart;
        $this->offPeakWindowEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;
    }

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /**
     * Попадает ли момент времени (UTC) в окно [window_start, window_end)?
     * окно может переходить через полночь (16:30–00:30).
     */
    public function isInOffPeak(\DateTimeImmutable $tsUtc): bool
    {
        $start = $this->offPeakWindowStart;
        $end = $this->offPeakWindowEnd;
        if ($start === null || $end === null) {
            return false;
        }

        // Формат валидирован на входе: 'HH:MM', поэтому '16:30' → 1630.
        $t = (int) str_replace(':', '', $tsUtc->format('H:i'));
        $s = (int) str_replace(':', '', $start);
        $e = (int) str_replace(':', '', $end);

        return $s <= $e
            ? $t >= $s && $t < $e
            : $t >= $s || $t < $e;
    }
}
