<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Billing\CostCalculator;
use App\Domain\Billing\OffPeak;
use App\Domain\Billing\RateUnit;
use App\Domain\Billing\TokenCounts;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Тесты ядра тарификации: воспроизводит сценарии из TASK.md (e1, e2, e3)
 * и проверит правила оф-пика и HALF_UP-округления.
 */
final class CostCalculatorTest extends TestCase
{
    /**
     * @return array<string, array{
     *     name:string, tokens:array<string,int>, unit:RateUnit, rates:array<string,string>,
     *     offPeak:OffPeak|null, minute:int, expected:string
     * }>
     */
    public static function rateProvider(): array
    {
        $perTokenRate = ['input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75'];
        return [
            'e1 per_1m_tokens peak' => [
                'tokens' => ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
                'unit' => RateUnit::Per1mTokens,
                'rates' => $perTokenRate,
                'offPeak' => OffPeak::fromWindowAndMultiplier('16:30', '00:30', '0.5'),
                'minute' => 11 * 60,
                'expected' => '14.262499',
            ],
            'e2 per_1m_tokens new-line peak' => [
                'tokens' => ['input' => 2000000, 'output' => 1000000, 'cache_read' => 0, 'cache_write' => 0],
                'unit' => RateUnit::Per1mTokens,
                'rates' => ['input' => '1.5', 'output' => '8.0', 'cache_read' => '0.15', 'cache_write' => '1.875'],
                'offPeak' => OffPeak::fromWindowAndMultiplier('16:30', '00:30', '0.5'),
                'minute' => 12 * 60,
                'expected' => '11.000000',
            ],
            'e3 unified' => [
                'tokens' => ['input' => 3000000, 'output' => 500000, 'cache_read' => 0, 'cache_write' => 0],
                'unit' => RateUnit::Per1mTokensUnified,
                'rates' => ['any' => '2.4'],
                'offPeak' => null,
                'minute' => 17 * 60 + 10,
                'expected' => '8.400000',
            ],
            'off-peak halves the event' => [
                'tokens' => ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
                'unit' => RateUnit::Per1mTokens,
                'rates' => $perTokenRate,
                'offPeak' => OffPeak::fromWindowAndMultiplier('16:30', '00:30', '0.5'),
                'minute' => 17 * 60,
                'expected' => '7.131249',
            ],
            'half-up rounds the final cost' => [
                'tokens' => ['input' => 1000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
                'unit' => RateUnit::Per1mTokens,
                'rates' => ['input' => '1.06251234', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
                'offPeak' => null,
                'minute' => 0,
                'expected' => '1.062512',
            ],
            'zero tokens zero cost' => [
                'tokens' => ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
                'unit' => RateUnit::Per1mTokens,
                'rates' => $perTokenRate,
                'offPeak' => null,
                'minute' => 0,
                'expected' => '0.000000',
            ],
        ];
    }

    #[DataProvider('rateProvider')]
    public function testRate(array $tokens, RateUnit $unit, array $rates, ?OffPeak $offPeak, int $minute, string $expected): void
    {
        $calc = new CostCalculator();
        $result = $calc->rate(TokenCounts::fromArray($tokens), $unit, $rates, $offPeak, $minute);
        self::assertSame($expected, $result);
        // Итог всегда ровно 6 знаков после точки.
        self::assertMatchesRegularExpression('/^.+\.\d{6}$/', $result);
    }

    public function testRateIsExactNoFloatingPoint(): void
    {
        $calc = new CostCalculator();
        // 1 200 000 × 3 / 1e6 = 3.6 — именно 3.600000, не 3.5999999...
        $result = $calc->rate(
            TokenCounts::fromArray(['input' => 1200000]),
            RateUnit::Per1mTokens,
            ['input' => '3', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
            null,
            0,
        );
        self::assertSame('3.600000', $result);
    }
}
