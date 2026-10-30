<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Billing\Cost;
use App\Domain\Billing\TokenMix;
use App\Domain\Pricing\BillingUnit;
use App\Domain\Pricing\OffPeakPolicy;
use App\Domain\Pricing\PriceRow;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Проверяет стоимость события (TASK.md «Правила тарификации»):
 *  - per_1m_tokens:  cost = Σ tokens_вид × rate_вид / 1 000 000;
 *  - per_1m_tokens_unified: cost = (Σ tokens) × rate_any / 1 000 000;
 *  - оф-пик:  multiplier, если ts входит в [window_start, window_end);
 *  - единственное округление:  HALF_UP 6 знаков на итоговую стоимость события.
 */
final class RateComputationTest extends TestCase
{
    private function per1mTokenRow(array $rates): PriceRow
    {
        return new PriceRow(
            providerCode: 'prism',
            modelCode: 'm1',
            validFrom: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            currency: 'USD',
            unit: BillingUnit::PER_1M_TOKENS,
            rates: [
                'input' => Cost::make((string) $rates['input']),
                'output' => Cost::make((string) $rates['output']),
                'cache_read' => Cost::make((string) $rates['cache_read']),
                'cache_write' => Cost::make((string) $rates['cache_write']),
            ],
        );
    }

    private function unifiedRow(string $any): PriceRow
    {
        return new PriceRow(
            providerCode: 'prism',
            modelCode: 'm2',
            validFrom: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            currency: 'USD',
            unit: BillingUnit::PER_1M_TOKENS_UNIFIED,
            rates: ['any' => Cost::make($any)],
        );
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: int, 3: int, 4: ?array, 5: string}>
     */
    public static function cases(): array
    {
        return [
            'per token: 1.2M+0.8M+2.5M+83333 @ 3/12/0.3/3.75 = 14.262499' => [
                1_200_000, 800_000, 2_500_000, 83_333,
                ['input' => '3', 'output' => '12', 'cache_read' => '0.3', 'cache_write' => '3.75'],
                '14.262499',
            ],
            'unified: 3.5M tokens @ 2.4 = 8.400000' => [
                3_000_000, 500_000, 0, 0,
                ['any' => '2.4'],
                '8.400000',
            ],
        ];
    }

    #[DataProvider('cases')]
    public function testRateCalculation(
        int $ti,
        int $to,
        int $tcr,
        int $tcw,
        array $rates,
        string $expected,
    ): void {
        if (isset($rates['input'])) {
            $row = $this->per1mTokenRow($rates);
        } else {
            $row = $this->unifiedRow($rates['any']);
        }

        $base = $row->unit->calculateCost(new TokenMix($ti, $to, $tcr, $tcw), $row->rates);
        $result = Cost::roundHalfUp6($base);

        self::assertSame($expected, $result->toSixDecimalsTruncated(), 'rate computation mismatch');
    }

    public function testOffpeakInsideWindow(): void
    {
        $r1 = Cost::make('1');
        $r2 = Cost::make('0.5');
        $offpeak = new OffPeakPolicy('16:30', '00:30', $r2);

        // 17:00 UTC — inside [16:30, 00:30)
        $ts = new \DateTimeImmutable('2026-09-15T17:00:00Z');
        $applies = $offpeak->appliesAt($ts);
        self::assertTrue($applies, 'offpeak should apply inside window');
    }

    public function testOffpeakOutsideWindow(): void
    {
        $r1 = Cost::make('1');
        $r2 = Cost::make('0.5');
        $offpeak = new OffPeakPolicy('16:30', '00:30', $r2);

        // 12:00 UTC — outside [16:30, 00:30)
        $ts = new \DateTimeImmutable('2026-09-15T12:00:00Z');
        $applies = $offpeak->appliesAt($ts);
        self::assertFalse($applies, 'offpeak should not apply outside window');
    }
}
