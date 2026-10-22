<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Support\OffPeak;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Тест оф-пикового окна: старт включительно, конец исключительно,
 * переход через полночь, отсутствие окна.
 */
final class OffPeakTest extends TestCase
{
    private function ts(string $hhmm): DateTimeImmutable
    {
        return (new DateTimeImmutable('2026-09-15T00:00:00Z'))->setTimezone(new \DateTimeZone('UTC'))
            ->modify('today ' . $hhmm . ':00');
    }

    #[DataProvider('windowProvider')]
    public function test_window_boundaries(string $hhmm, bool $expectedInWindow): void
    {
        // окно 16:30..00:30 пересекает полночь
        $in = OffPeak::isInWindow($this->ts($hhmm), '16:30', '00:30');
        self::assertSame($expectedInWindow, $in, "время $hhmm в окне 16:30-00:30? " . ($in ? 'да' : 'нет'));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function windowProvider(): array
    {
        return [
            '17:00 inside'         => ['17:00', true],
            '16:30 inclusive'      => ['16:30', true],
            '16:29 before start'   => ['16:29', false],
            '23:59 inside'         => ['23:59', true],
            '00:00 after midnight' => ['00:00', true],
            '00:29 inside'         => ['00:29', true],
            '00:30 exclusive'      => ['00:30', false],
            '11:30 daytime'        => ['11:30', false],
        ];
    }

    public function test_no_window_when_missing(): void
    {
        self::assertFalse(OffPeak::isInWindow($this->ts('17:00'), null, null));
        self::assertFalse(OffPeak::isInWindow($this->ts('17:00'), '16:30', null));
        self::assertFalse(OffPeak::isInWindow($this->ts('17:00'), null, '00:30'));
    }

    public function test_same_day_window_24_end(): void
    {
        // то же окно через "24:00" - эквивалент 20:00..24:00
        self::assertTrue(OffPeak::isInWindow($this->ts('21:00'), '20:00', '24:00'));
        self::assertFalse(OffPeak::isInWindow($this->ts('19:59'), '20:00', '24:00'));
        self::assertFalse(OffPeak::isInWindow($this->ts('00:00'), '20:00', '24:00'));
    }
}
