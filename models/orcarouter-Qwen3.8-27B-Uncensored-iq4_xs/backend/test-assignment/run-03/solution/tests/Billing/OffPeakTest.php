<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Provider\Provider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private function provider(?string $start, ?string $end, string $mult = '0.5'): Provider
    {
        $p = new Provider();
        $p->setOffPeak($start, $end, $mult);

        return $p;
    }

    #[DataProvider('cases')]
    public function testWindow(string $start, string $end, int $minutes, bool $expected): void
    {
        self::assertSame($expected, $this->provider($start, $end)->isOffPeak($minutes));
    }

    public static function cases(): iterable
    {
        // Окно 16:30–00:30 (переход через полночь), end-исключительно.
        yield 'in-window-17:00' => ['16:30', '00:30', 17 * 60, true];
        yield 'in-window-23:00' => ['16:30', '00:30', 23 * 60, true];
        yield 'in-window-00:29' => ['16:30', '00:30', 29, true];
        yield 'boundary-start-16:30' => ['16:30', '00:30', 16 * 60 + 30, true];
        yield 'boundary-end-00:30' => ['16:30', '00:30', 30, false];
        yield 'outside-12:00' => ['16:30', '00:30', 12 * 60, false];
        yield 'outside-16:29' => ['16:30', '00:30', 16 * 60 + 29, false];

        // Окно без перехода: 08:00–12:00.
        yield 'same-day-in' => ['08:00', '12:00', 9 * 60, true];
        yield 'same-day-start' => ['08:00', '12:00', 8 * 60, true];
        yield 'same-day-end' => ['08:00', '12:00', 12 * 60, false];
        yield 'same-day-before' => ['08:00', '12:00', 7 * 60, false];
        yield 'same-day-after' => ['08:00', '12:00', 13 * 60, false];
    }

    public function testNoOffPeak(): void
    {
        $p = new Provider();
        $p->setOffPeak(null, null, null);
        self::assertFalse($p->isOffPeak(17 * 60));
    }
}
