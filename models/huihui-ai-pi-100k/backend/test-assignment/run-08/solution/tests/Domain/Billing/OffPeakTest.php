<?php

declare(strict_types=1);

namespace App\Tests\Domain\Billing;

use App\Domain\Billing\OffPeak;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private OffPeak $window;

    protected function setUp(): void
    {
        // Окно prism-а: 16:30–00:30 UTC (через полночь).
        $this->window = new OffPeak('16:30', '00:30', '0.5');
    }

    private function at(string $hms): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('H:i:s', $hms, new DateTimeZone('UTC'))
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('2026-09-16');
    }

    #[Test]
    public function windowBoundariesAreInclusiveStartExclusiveEnd(): void
    {
        self::assertFalse($this->window->appliesAt($this->at('16:29:59')));
        self::assertTrue($this->window->appliesAt($this->at('16:30:00')));
        self::assertTrue($this->window->appliesAt($this->at('23:59:00')));
        self::assertTrue($this->window->appliesAt($this->at('00:15:00')));
        self::assertFalse($this->window->appliesAt($this->at('00:30:00')));
        self::assertFalse($this->window->appliesAt($this->at('00:31:00')));
        self::assertFalse($this->window->appliesAt($this->at('12:00:00')));
    }

    #[Test]
    public function plainWindow(): void
    {
        $w = new OffPeak('09:00', '17:00', '0.9');
        self::assertFalse($w->appliesAt($this->at('08:59:00')));
        self::assertTrue($w->appliesAt($this->at('09:00:00')));
        self::assertTrue($w->appliesAt($this->at('16:59:00')));
        self::assertFalse($w->appliesAt($this->at('17:00:00')));
    }
}
