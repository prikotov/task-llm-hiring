<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Типизированное время (UTC).
 */
final class Instant
{
    private \DateTimeImmutable $dt;
    private string $normalized;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new \InvalidArgumentException('Instant cannot be empty');
        }

        try {
            $dt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $trimmed);
        } catch (\Exception) {
            $dt = new \DateTimeImmutable($trimmed);
        }

        if ($dt === false) {
            throw new \InvalidArgumentException("Invalid instant: $value");
        }

        $this->dt = $dt->setTimezone(new \DateTimeZone('UTC'));
        $this->normalized = $this->dt->format(\DateTimeInterface::ATOM);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dt;
    }

    /**
     * Время суток в секундах от полуночи UTC [0, 86400).
     */
    public function secondsOfDay(): int
    {
        return (int) $this->dt->format('H') * 3600
            + (int) $this->dt->format('i') * 60
            + (int) $this->dt->format('s');
    }

    public function isBefore(self $other): bool
    {
        return $this->dt < $other->dt;
    }

    public function equals(self $other): bool
    {
        return $this->dt == $other->dt;
    }

    public function toString(): string
    {
        return $this->normalized;
    }
}
