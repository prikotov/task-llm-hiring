<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Input;
use App\Domain\Tariff;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TariffTest extends TestCase
{
    public function testExampleAndHalfUp(): void
    {
        $tariff = new Tariff();
        self::assertSame('14.262499', $tariff->calculate(
            ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
            'per_1m_tokens',
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            null,
            '2026-09-15T11:00:00Z'
        ));
        $tokens = ['input' => 1, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0];
        self::assertSame('0.000001', $tariff->calculate($tokens, 'per_1m_tokens_unified', ['any' => '0.500000'], null, '2026-09-15T11:00:00Z'));
        self::assertSame('0.000000', $tariff->calculate($tokens, 'per_1m_tokens_unified', ['any' => '0.499999'], null, '2026-09-15T11:00:00Z'));
        self::assertSame('0.000000', $tariff->calculate(
            $tokens,
            'per_1m_tokens_unified',
            ['any' => '0.999999'],
            ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0.5'],
            '2026-09-15T17:00:00Z'
        ));
    }

    #[DataProvider('windowCases')]
    public function testWindow(string $time, string $start, string $end, bool $expected): void
    {
        self::assertSame($expected, (new Tariff())->inWindow($time, $start, $end));
    }

    public static function windowCases(): iterable
    {
        yield ['16:29:59', '16:30', '00:30', false];
        yield ['16:30:00', '16:30', '00:30', true];
        yield ['23:59:59', '16:30', '00:30', true];
        yield ['00:00:00', '16:30', '00:30', true];
        yield ['00:29:59', '16:30', '00:30', true];
        yield ['00:30:00', '16:30', '00:30', false];
        yield ['09:00:00', '09:00', '10:00', true];
        yield ['10:00:00', '09:00', '10:00', false];
        yield ['09:00:00', '09:00', '09:00', false];
    }

    public function testUnifiedAndLargeIntegers(): void
    {
        self::assertSame('36893488147419.103228', (new Tariff())->calculate(
            array_fill_keys(Tariff::KINDS, PHP_INT_MAX),
            'per_1m_tokens_unified',
            ['any' => '1.000000'],
            null,
            '2026-09-15T11:00:00Z'
        ));
    }

    public function testTimestampNormalization(): void
    {
        self::assertSame('2026-09-15T12:00:00.100000Z', Input::timestamp('2026-09-15T12:00:00.1Z'));
        $this->expectException(\InvalidArgumentException::class);
        Input::timestamp('2026-02-30T12:00:00Z');
    }
}
