<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeakWindow;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function utc(string $hhmm): \DateTimeImmutable
    {
        return new \DateTimeImmutable("2026-09-15T{$hhmm}:00Z", new \DateTimeZone('UTC'));
    }

    /**
     * Окно 16:30–00:30 (переходит через полночь).
     */
    public function testMidnightCrossingWindow(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');

        self::assertFalse($window->appliesTo($this->utc('16:29')), 'до старта окна');
        self::assertTrue($window->appliesTo($this->utc('16:30')), 'старт окна включительно');
        self::assertTrue($window->appliesTo($this->utc('23:59')));
        self::assertTrue($window->appliesTo($this->utc('00:29')), 'после полуночи — всё ещё окно');
        self::assertFalse($window->appliesTo($this->utc('00:30')), 'конец окна исключительный');
        self::assertFalse($window->appliesTo($this->utc('11:00')), 'вне окна (пик)');
    }

    /**
     * Обычное окно без перехода через полночь.
     */
    public function testSimpleWindow(): void
    {
        $window = new OffPeakWindow('09:00', '17:00', '0.7');

        self::assertFalse($window->appliesTo($this->utc('08:59')));
        self::assertTrue($window->appliesTo($this->utc('09:00')));
        self::assertTrue($window->appliesTo($this->utc('16:59')));
        self::assertFalse($window->appliesTo($this->utc('17:00')));
    }

    public function testSecondsDoNotMatter(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');

        self::assertTrue($window->appliesTo(new \DateTimeImmutable('2026-09-15T16:30:59Z', new \DateTimeZone('UTC'))));
    }

    public function testDegenerateWindowIsEmpty(): void
    {
        $window = new OffPeakWindow('12:00', '12:00', '0.5');

        self::assertFalse($window->appliesTo($this->utc('12:00')));
    }

    public function testInvalidWindowFormatIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OffPeakWindow('25:00', '00:30', '0.5');
    }

    public function testMultiplierMustBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OffPeakWindow('16:30', '00:30', '0');
    }
}
