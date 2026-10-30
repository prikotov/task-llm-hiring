<?php

declare(strict_types=1);

namespace App\Tests\Domain\Billing;

use App\Domain\Billing\OffPeak;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Окно скидок провайдера (TASK.md, правило 3):
 * [start, end), переход через полночь, «окно на весь день» при start == end.
 */
final class OffPeakTest extends TestCase
{
    private const WINDOW = ['16:30', '00:30', '0.5'];

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function windowCases(): iterable
    {
        // 16:30 включительно — в окне
        yield 'start inclusive' => ['2026-09-15T16:30:00Z', true];
        yield 'midday off-peak' => ['2026-09-15T17:15:00Z', true];
        yield 'midnight crossing inside' => ['2026-09-15T00:29:00Z', true];
        // 00:30 исключительно — вне окна
        yield 'end exclusive' => ['2026-09-15T00:30:00Z', false];
        yield 'before start' => ['2026-09-15T16:29:59Z', false];
        yield 'plain day time' => ['2026-09-15T11:00:00Z', false];
    }

    #[Test]
    #[DataProvider('windowCases')]
    public function windowSemantics(string $isoUtc, bool $expectedActive): void
    {
        $offPeak = OffPeak::fromWindow(self::WINDOW[0], self::WINDOW[1], self::WINDOW[2]);
        self::assertNotNull($offPeak);

        self::assertSame(
            $expectedActive,
            $offPeak->isActiveAt(new \DateTimeImmutable($isoUtc)),
        );
    }

    #[Test]
    public function equalBoundsMeansWholeDay(): void
    {
        $offPeak = OffPeak::fromWindow('04:00', '04:00', '0.9');
        self::assertNotNull($offPeak);
        self::assertTrue($offPeak->isActiveAt(new \DateTimeImmutable('2026-01-01T04:00:00Z')));
        self::assertTrue($offPeak->isActiveAt(new \DateTimeImmutable('2026-01-01T23:59:59Z')));
    }

    #[Test]
    public function nullWhenProviderHasNoPolicy(): void
    {
        self::assertNull(OffPeak::fromWindow(null, null, null));
    }

    #[Test]
    public function rejectsMalformedBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        OffPeak::fromWindow('24:00', '00:30', '0.5');
    }
}
