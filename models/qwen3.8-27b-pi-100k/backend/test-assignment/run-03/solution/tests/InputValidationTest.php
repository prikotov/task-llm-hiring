<?php

declare(strict_types=1);

namespace App\Tests;

use App\Shared\Decimals;
use App\Shared\InputFormatException;
use App\Shared\IsoUtc;
use PHPUnit\Framework\TestCase;

final class InputValidationTest extends TestCase
{
    public function testDecimalsAcceptedFormats(): void
    {
        self::assertSame('3.000000', Decimals::validate('3.000000', 6, 't'));
        self::assertSame('0', Decimals::validate('0', 6, 't'));
        self::assertSame('0.5', Decimals::validate('0.5', 16, 't'));
        self::assertSame('0.300000', Decimals::validate('0.300000', 6, 't'));
    }

    public function testDecimalsRejectBadFormats(): void
    {
        $cases = ['1e3', '-1', '1.2.3', 'abc', '1.1234567', ' 1.5', '.5'];
        $rejected = 0;
        foreach ($cases as $case) {
            try {
                Decimals::validate($case, 6, 't');
            } catch (InputFormatException) {
                ++$rejected;
                continue;
            }
            self::fail('не отклонено: '.$case);
        }
        self::assertSame(count($cases), $rejected);
    }

    public function testIsoUtcAcceptedFormats(): void
    {
        $parsed = IsoUtc::parse('2026-09-15T12:00:00Z', 't');
        self::assertSame('2026-09-15 12:00:00', $parsed->format('Y-m-d H:i:s'));
        self::assertSame('+00:00', $parsed->format('P'));

        $parsed = IsoUtc::parse('2026-09-15T12:00:00.123456Z', 't');
        self::assertSame('123456', $parsed->format('u'));
    }

    public function testIsoUtcRejectsNonUtcAndBadDates(): void
    {
        $cases = [
            '2026-09-15T14:00:00+02:00', // смещение таймзоны
            '2026-13-01T00:00:00Z',      // невалидный месяц
            '15/09/2026 12:00',          // не ISO-8601
            'not-a-date',
            '2026-09-15T12:00:00.1234567Z', // больше 6 знаков дробной части
        ];
        $rejected = 0;
        foreach ($cases as $case) {
            try {
                IsoUtc::parse($case, 't');
            } catch (InputFormatException) {
                ++$rejected;
                continue;
            }
            self::fail('не отклонено: '.$case);
        }
        self::assertSame(count($cases), $rejected);
    }
}
