<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Shared\Exception\BadInputException;
use App\Support\TimeOfDay;
use PHPUnit\Framework\TestCase;

final class TimeOfDayTest extends TestCase
{
    public function testParsesMinutesOfDay(): void
    {
        self::assertSame(0, TimeOfDay::parseHhMm('00:00'));
        self::assertSame(60, TimeOfDay::parseHhMm('01:00'));
        self::assertSame(990, TimeOfDay::parseHhMm('16:30'));
        self::assertSame(1439, TimeOfDay::parseHhMm('23:59'));
    }

    public function testRejectsHourTwentyFour(): void
    {
        $this->expectException(BadInputException::class);
        TimeOfDay::parseHhMm('24:00');
    }

    public function testRejectsMinuteSixty(): void
    {
        $this->expectException(BadInputException::class);
        TimeOfDay::parseHhMm('16:60');
    }

    public function testRejectsSingleDigitHour(): void
    {
        $this->expectException(BadInputException::class);
        TimeOfDay::parseHhMm('7:30');
    }

    public function testRejectsEmptyValue(): void
    {
        $this->expectException(BadInputException::class);
        TimeOfDay::parseHhMm('');
    }
}
