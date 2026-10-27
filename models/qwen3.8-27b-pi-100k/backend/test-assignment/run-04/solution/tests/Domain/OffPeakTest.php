<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeak;
use App\Exception\ImportException;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    /** prism: 16:30–00:30, множитель 0.5 (окно через полночь). */
    private OffPeak $prism;

    protected function setUp(): void
    {
        $this->prism = OffPeak::create('16:30', '00:30', '0.5');
    }

    private function at(string $hhmm): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-09-15T' . $hhmm . ':00', new \DateTimeZone('UTC'));
    }

    public function test_window_start_is_inclusive(): void
    {
        self::assertTrue($this->prism->appliesAt($this->at('16:30')));
        self::assertTrue($this->prism->appliesAt($this->at('16:31')));
        self::assertTrue($this->prism->appliesAt($this->at('23:59')));
    }

    public function test_window_crosses_midnight(): void
    {
        self::assertTrue($this->prism->appliesAt($this->at('00:00')));
        self::assertTrue($this->prism->appliesAt($this->at('00:29')));
    }

    public function test_window_end_is_exclusive(): void
    {
        self::assertFalse($this->prism->appliesAt($this->at('00:30')));
        self::assertFalse($this->prism->appliesAt($this->at('10:00')));
        self::assertFalse($this->prism->appliesAt($this->at('16:29')));
    }

    public function test_normal_window(): void
    {
        $w = OffPeak::create('02:00', '06:00', '0.25');
        self::assertTrue($w->appliesAt($this->at('02:00')));
        self::assertTrue($w->appliesAt($this->at('05:59')));
        self::assertFalse($w->appliesAt($this->at('06:00')));
        self::assertFalse($w->appliesAt($this->at('01:59')));
    }

    public function test_full_day_window(): void
    {
        $w = OffPeak::create('00:00', '24:00', '0.1');
        self::assertTrue($w->appliesAt($this->at('12:00')));
    }

    public function test_zero_length_window_is_empty(): void
    {
        $w = OffPeak::create('12:00', '12:00', '0.5');
        self::assertFalse($w->appliesAt($this->at('12:00')));
    }

    public function test_multiplier_is_preserved(): void
    {
        self::assertSame('0.5', $this->prism->multiplier());
    }

    public function test_invalid_time_rejected(): void
    {
        $this->expectException(ImportException::class);
        OffPeak::create('25:00', '01:00', '0.5');
    }

    public function test_invalid_multiplier_rejected(): void
    {
        $this->expectException(ImportException::class);
        OffPeak::create('01:00', '02:00', '-3');
    }
}
