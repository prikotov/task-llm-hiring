<?php

declare(strict_types=1);

namespace App\Tests;

use App\Billing\OffPeak;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private static function at(string $hhMm): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', '2026-09-15T'.$hhMm, new \DateTimeZone('UTC'));
    }

    public function testWindowCrossingMidnight(): void
    {
        $offPeak = new OffPeak('16:30', '00:30', '0.5');

        self::assertTrue($offPeak->contains(self::at('16:30')));   // start включён
        self::assertTrue($offPeak->contains(self::at('17:10')));
        self::assertTrue($offPeak->contains(self::at('23:59')));
        self::assertTrue($offPeak->contains(self::at('00:29')));
        self::assertFalse($offPeak->contains(self::at('00:30')));  // end исключён
        self::assertFalse($offPeak->contains(self::at('16:29')));
        self::assertFalse($offPeak->contains(self::at('09:00')));
    }

    public function testWindowWithoutMidnight(): void
    {
        $offPeak = new OffPeak('09:00', '17:00', '0.8');

        self::assertTrue($offPeak->contains(self::at('09:00')));
        self::assertTrue($offPeak->contains(self::at('16:59')));
        self::assertFalse($offPeak->contains(self::at('17:00')));
        self::assertFalse($offPeak->contains(self::at('08:59')));
    }

    public function testMultiplierIsPreservedAsString(): void
    {
        self::assertSame('0.5', (new OffPeak('16:30', '00:30', '0.5'))->getMultiplier());
        self::assertSame('0.5', (new OffPeak('16:30', '00:30', '0.500'))->getMultiplier());
    }

    public function testEachInvalidWindowIsRejected(): void
    {
        $cases = [
            ['25:00', '00:30', '0.5'],
            ['16:70', '00:30', '0.5'],
            ['16:30', '24:01', '0.5'],
            ['16:30', '00:30', '0'],
            ['16:30', '00:30', '-1'],
            ['16:30', '00:30', 'abc'],
            ['1630', '00:30', '0.5'],
            ['00:00', 'abc', '0.5'],
        ];
        foreach ($cases as [$s, $e, $m]) {
            $caught = false;
            try {
                new OffPeak($s, $e, $m);
            } catch (\InvalidArgumentException) {
                $caught = true;
            }
            self::assertTrue($caught, 'window "'.$s.'–'.$e.'" mult "'.$m.'" должна быть отклонена');
        }
    }
}
