<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Provider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private const WINDOW = ['start' => '16:30', 'end' => '00:30'];

    public function makeProvider(): Provider
    {
        $p = Provider::make('p', 'P');
        $p->setOffPeak(self::WINDOW['start'], self::WINDOW['end'], '0.5');

        return $p;
    }

    #[DataProvider('cases')]
    public function test_window_inclusion(string $time, bool $expected): void
    {
        $p = $this->makeProvider();
        [$h, $m, $s] = array_map('intval', explode(':', $time));
        $ts = (new \DateTimeImmutable('2026-01-15 00:00:00', new \DateTimeZone('UTC')))->setTime($h, $m, $s);

        self::assertSame($expected, $p->isInOffPeak($ts));
    }

    public static function cases(): iterable
    {
        yield 'peak' => ['11:00:00', false];
        yield 'start inclusive' => ['16:30:00', true];
        yield 'just before start' => ['16:29:59', false];
        yield 'middle of night' => ['23:59:59', true];
        yield 'after midnight' => ['00:29:59', true];
        yield 'end exclusive' => ['00:30:00', false];
        yield 'morning' => ['09:00:00', false];
    }

    public function test_no_discount_when_off_peak_absent(): void
    {
        $p = Provider::make('p', 'P');
        $p->setOffPeak(null, null, null);

        self::assertFalse($p->isInOffPeak(new \DateTimeImmutable('2026-01-15 17:00:00', new \DateTimeZone('UTC'))));
    }
}
