<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Billing\OffPeakPolicy;
use App\Domain\Shared\Decimal;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM со справочными данными: код, название, окно оф-пик по UTC.
 *
 * Окно оф-пик хранится в минутах от 00:00 UTC: начало включительно,
 * конец исключительно; окно может пересекать полночь (16:30–00:30 и т.п.).
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\TypedIdentifier]
    #[ORM\Column(type: 'bigint')]
    private null|int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    #[ORM\Column(type: 'text')]
    private string $name;

    #[ORM\Column(name: 'off_peak_start_minutes', type: 'integer', options: ['default' => null])]
    private null|int $offPeakStartMinutes = null;

    #[ORM\Column(name: 'off_peak_end_minutes', type: 'integer', options: ['default' => null])]
    private null|int $offPeakEndMinutes = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 32, options: ['default' => null])]
    private null|string $offPeakMultiplier = null;

    public function __construct(
        string $code,
        string $name,
        null|int $offPeakStartMinutes = null,
        null|int $offPeakEndMinutes = null,
        null|string $offPeakMultiplier = null,
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->offPeakStartMinutes = $offPeakStartMinutes;
        $this->offPeakEndMinutes = $offPeakEndMinutes;
        $this->offPeakMultiplier = $offPeakMultiplier;
    }

    /**
     * Идемпотентное обновление справочных полей (upsert по коду).
     */
    public function update(
        string $name,
        null|int $offPeakStartMinutes,
        null|int $offPeakEndMinutes,
        null|string $offPeakMultiplier,
    ): void {
        $this->name = $name;
        $this->offPeakStartMinutes = $offPeakStartMinutes;
        $this->offPeakEndMinutes = $offPeakEndMinutes;
        $this->offPeakMultiplier = $offPeakMultiplier;
    }

    public function getId(): null|int
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

    public function getOffPeakStartMinutes(): null|int
    {
        return $this->offPeakStartMinutes;
    }

    public function getOffPeakEndMinutes(): null|int
    {
        return $this->offPeakEndMinutes;
    }

    public function getOffPeakMultiplier(): null|string
    {
        return $this->offPeakMultiplier;
    }

    /**
     * Политика оф-пик провайдера или null, если скидки нет
     * (окно или множитель не заданы).
     */
    public function offPeakPolicy(): ?OffPeakPolicy
    {
        if (
            $this->offPeakStartMinutes === null
            || $this->offPeakEndMinutes === null
            || $this->offPeakMultiplier === null
            || Decimal::normalize($this->offPeakMultiplier) === '0'
        ) {
            return null;
        }

        return new OffPeakPolicy(
            $this->offPeakStartMinutes,
            $this->offPeakEndMinutes,
            Decimal::normalize($this->offPeakMultiplier),
        );
    }
}
