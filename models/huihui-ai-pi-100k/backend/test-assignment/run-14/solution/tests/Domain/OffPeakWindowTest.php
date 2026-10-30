<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeakWindow;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function minutes(string $hhmm): int
    {
        return ((int) substr($hhmm, 0, 2)) * 60 + ((int) substr($hhmm, 3, 2));
    }

    private function window(string $start, string $end): OffPeakWindow
    {
        return new OffPeakWindow($this->minutes($start), $this->minutes($end), '0.500000');
    }

    #[Test]
    public function withinPeakNormalWindow(): void
    {
        $w = $this->window('08:00', '12:00');
        self::assertTrue($w->contains($this->minutes('09:00')));
        self::assertTrue($w->contains($this->minutes('08:00'))); // start включительно
        self::assertFalse($w->contains($this->minutes('12:00'))); // end исключительно
    }

    #[Test]
    public function offPeakCrossesMidnight(): void
    {
        // Окно 16:30–00:30 (через полночь):
        //  00:00..00:30 → в окне,
        //  00:30..16:30 → вне окна,
        //  16:30..23:59 → в окне.
        $w = $this->window('16:30', '00:30');

        self::assertTrue($w->contains($this->minutes('00:00')));
        self::assertTrue($w->contains($this->minutes('00:29')));
        self::assertFalse($w->contains($this->minutes('00:30')));

        self::assertFalse($w->contains($this->minutes('09:00')));

        self::assertTrue($w->contains($this->minutes('16:30')));
        self::assertTrue($w->contains($this->minutes('16:31')));
        self::assertFalse($w->contains($this->minutes('16:29')));

        self::assertTrue($w->contains($this->minutes('23:59')));
        self::assertFalse($w->contains($this->minutes('12:00')));
    }

    #[Test]
    public function emptyWindowAlwaysFalse(): void
    {
        $w = $this->window('00:00', '00:00'); // «пустое» окно (начало = конец)
        self::assertFalse($w->contains(0));
        self::assertFalse($w->contains(7 * 60));
    }
}
