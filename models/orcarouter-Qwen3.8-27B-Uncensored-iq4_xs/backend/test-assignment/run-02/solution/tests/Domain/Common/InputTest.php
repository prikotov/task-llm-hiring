<?php

declare(strict_types=1);

namespace App\Tests\Domain\Common;

use App\Domain\Common\ImportFormatException;
use App\Domain\Common\Input;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InputTest extends TestCase
{
    #[Test]
    public function parsesValidIsoUtcTimestamps(): void
    {
        $parsed = Input::parseIsoUtc('2026-09-15T09:00:00Z');
        self::assertIsObject($parsed);
        self::assertSame('2026-09-15T09:00:00+00:00', $parsed->format('c'));

        $parsed = Input::parseIsoUtc('2026-09-01T00:00:00.123456Z');
        self::assertIsObject($parsed);
        self::assertSame('123456', $parsed->format('u'));
    }

    #[Test]
    public function rejectsNonIso8601AndNonUtcTimestamps(): void
    {
        foreach ([
            '2026-09-15 09:00:00Z',
            '2026-09-15T09:00:00+02:00',
            '2026-13-15T09:00:00Z',
            '2026-02-30T09:00:00Z',
            '2026-09-15T24:00:00Z',
            '2026-09-15T09:61:00Z',
            'not a date',
            '2026-09-15T09:00:00',
        ] as $value) {
            self::assertNull(Input::parseIsoUtc($value), $value);
        }
    }

    #[Test]
    public function hhmmValidation(): void
    {
        self::assertTrue(Input::isHhmm('09:00'));
        self::assertTrue(Input::isHhmm('23:59'));
        self::assertTrue(Input::isHhmm('00:00'));

        foreach (['9:00', '24:00', '09:60', 'abc', '24:01', '09-00', ''] as $value) {
            self::assertFalse(Input::isHhmm($value), $value);
        }

        self::assertSame(60, Input::hhmmToMinutes('01:00'));
        self::assertSame(23 * 60 + 30, Input::hhmmToMinutes('23:30'));
    }

    #[Test]
    public function decimalStringValidation(): void
    {
        self::assertTrue(Input::isDecimalString('3.000000'));
        self::assertTrue(Input::isDecimalString('0'));
        self::assertTrue(Input::isDecimalString('123'));
        self::assertFalse(Input::isDecimalString('1.2.3'));
        self::assertFalse(Input::isDecimalString('-1'));
        self::assertFalse(Input::isDecimalString('1e3'));
        self::assertFalse(Input::isDecimalString(''));
        // лимит дробной части
        self::assertFalse(Input::isDecimalString('1.2345678'));
    }

    #[Test]
    public function asRowRejectsNonObjects(): void
    {
        foreach ([[], 'str', 3, null] as $value) {
            try {
                Input::asRow($value, 'x');
            } catch (ImportFormatException) {
                continue;
            }
            $this->fail('Значение должно быть отклонено');
        }

        self::assertSame(['a' => 1], Input::asRow(['a' => 1], 'x'));
    }

    private function assertThrowsFmt(callable $fn): void
    {
        try {
            $fn();
        } catch (ImportFormatException) {
            return;
        }
        $this->fail('Ожидался ImportFormatException');
    }

    #[Test]
    public function asNonEmptyString(): void
    {
        self::assertSame('code', Input::asNonEmptyString('code', 'x'));

        foreach (['', 3, null, []] as $value) {
            $this->assertThrowsFmt(static fn () => Input::asNonEmptyString($value, 'x'));
        }
    }

    #[Test]
    public function asNonNegativeInt(): void
    {
        self::assertSame(0, Input::asNonNegativeInt(0, 'x'));
        self::assertSame(10, Input::asNonNegativeInt(10, 'x'));

        foreach ([-1, 1.5, '2', null, true] as $value) {
            $this->assertThrowsFmt(static fn () => Input::asNonNegativeInt($value, 'x'));
        }
    }
}
