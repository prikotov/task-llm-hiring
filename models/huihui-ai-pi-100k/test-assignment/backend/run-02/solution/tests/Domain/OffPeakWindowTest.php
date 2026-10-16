<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeakWindow;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function at(string $timeOfDay): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-10-05T' . $timeOfDay . ':00+00:00');
    }

    public function testWindowCrossingMidnight(): void
    {
        $window = OffPeakWindow::parse('16:30', '00:30', '0.5'); // из fixtures

        self::assertFalse($window->covers($this->at('16:29')));
        self::assertTrue($window->covers($this->at('16:30'))); // start включительно
        self::assertTrue($window->covers($this->at('23:59')));
        self::assertTrue($window->covers($this->at('00:00'))); // перешли полночь
        self::assertTrue($window->covers($this->at('00:29')));
        self::assertFalse($window->covers($this->at('00:30'))); // end исключительно
        self::assertFalse($window->covers($this->at('09:00')));
        self::assertFalse($window->covers($this->at('11:00')));
    }

    public function testNormalWindowWithoutMidnight(): void
    {
        $window = OffPeakWindow::parse('02:00', '06:00', '0.75');

        self::assertFalse($window->covers($this->at('01:59')));
        self::assertTrue($window->covers($this->at('02:00')));
        self::assertTrue($window->covers($this->at('05:59')));
        self::assertFalse($window->covers($this->at('06:00')));
        self::assertFalse($window->covers($this->at('23:59'))); // не «обёртывается»
    }

    public function testEmptyWindowNeverCovers(): void
    {
        $window = OffPeakWindow::parse('05:00', '05:00', '0.5');

        self::assertFalse($window->covers($this->at('05:00')));
        self::assertFalse($window->covers($this->at('12:00')));
    }

    public function testParseTimeOfDay(): void
    {
        self::assertSame(0, OffPeakWindow::parseTimeOfDay('00:00', 'x'));
        self::assertSame(23 * 3600 + 59 * 60, OffPeakWindow::parseTimeOfDay('23:59', 'x'));
        self::assertSame(16 * 3600 + 30 * 60, OffPeakWindow::parseTimeOfDay('16:30', 'x'));
    }

    #[DataProvider('badTimeOfDay')]
    public function testParseTimeOfDayRejects(string $value): void
    {
        $this->expectException(\ValueError::class);

        OffPeakWindow::parseTimeOfDay($value, 'field');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function badTimeOfDay(): iterable
    {
        yield '24:00' => ['24:00'];
        yield 'нет минут' => ['12'];
        yield 'минут больше 59' => ['16:60'];
        yield 'буквы' => ['ab:cd'];
        yield 'дефис' => ['16-30'];
        yield 'точка' => ['16.30'];
    }

    public function testMultiplierIsStored(): void
    {
        self::assertSame('0.5', OffPeakWindow::parse('10:00', '12:00', '0.5')->multiplier());
        self::assertSame('1', OffPeakWindow::parse('10:00', '12:00', '1')->multiplier());
    }

    #[DataProvider('badMultipliers')]
    public function testMultipliersRejected(string $multiplier): void
    {
        $this->expectException(\ValueError::class);

        OffPeakWindow::parse('10:00', '12:00', $multiplier);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function badMultipliers(): iterable
    {
        yield 'буквы' => ['abc'];
        yield 'лишние символы' => ['0.5x'];
        yield 'отрицательный' => ['-1'];
        yield 'запятая' => ['1,5'];
        yield 'пусто' => [''];
    }
}
