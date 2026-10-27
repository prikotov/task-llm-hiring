<?php

declare(strict_types=1);

namespace App\Domain\Provider;

use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM + параметр оф-пик (окно скидки по UTC).
 *
 * Окно: [window_start, window_end) минут от 00:00 UTC в пределах суток;
 * может переходить через полночь (например, 16:30–00:30).
 * Multiplier — множитель стоимости события, попавшего в окно.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $name;

    /** null — оф-пик у провайдера отсутствует */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $offPeak = null;

    final public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function id(): ?int
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

    public function offPeakSettings(): ?OffPeakWindow
    {
        if ($this->offPeak === null) {
            return null;
        }

        return new OffPeakWindow(
            $this->offPeak['window_start'],
            $this->offPeak['window_end'],
            $this->offPeak['multiplier'],
        );
    }

    final public function setOffPeak(
        ?string $windowStart,
        ?string $windowEnd,
        ?string $multiplier,
    ): void {
        $this->offPeak = (null === $windowStart || null === $windowEnd || null === $multiplier)
            ? null
            : ['window_start' => $windowStart, 'window_end' => $windowEnd, 'multiplier' => $multiplier];
    }

    /** @return array{window_start: string, window_end: string, multiplier: string}|null */
    public function offPeakRaw(): ?array
    {
        return $this->offPeak;
    }
}
