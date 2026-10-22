<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\CostCalculator;
use App\Domain\Model;
use App\Domain\OffPeak;
use App\Domain\Price;
use App\Domain\Provider;
use App\Domain\TokenUsage;
use App\Infrastructure\Input;
use App\Infrastructure\Doctrine\UtcInstantType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BillingTest extends TestCase
{
    private function price(string $unit, array $rates): Price
    {
        return new Price(new Model(new Provider('p', 'Провайдер', null), 'm'), Input::instant('2026-08-01T00:00:00Z'), 'USD', $unit, $rates);
    }

    public function testSplitRatesAndSingleRounding(): void
    {
        $price = $this->price(Price::SPLIT, ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000']);
        $tokens = new TokenUsage(1200000, 800000, 2500000, 83333);
        $calculator = new CostCalculator();
        self::assertSame('14.262499', $calculator->calculate($tokens, $price, '1'));
        self::assertSame('7.131249', $calculator->calculate($tokens, $price, '0.5'));
    }

    public function testUnifiedIncludesEveryKindAndAvoidsIntegerOverflow(): void
    {
        $price = $this->price(Price::UNIFIED, ['any' => '2.400000']);
        $tokens = new TokenUsage(PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MAX);
        self::assertSame('88544371553805.847747', (new CostCalculator())->calculate($tokens, $price, '1'));
    }

    #[DataProvider('roundingCases')]
    public function testRounding(string $rate, string $multiplier, string $expected): void
    {
        self::assertSame($expected, (new CostCalculator())->calculate(
            new TokenUsage(1, 0, 0, 0),
            $this->price(Price::UNIFIED, ['any' => $rate]),
            $multiplier,
        ));
    }

    public static function roundingCases(): iterable
    {
        yield ['0.499999', '1', '0.000000'];
        yield ['0.500000', '1', '0.000001'];
        yield ['2.500000', '1', '0.000003'];
        yield ['0.999999', '0.5', '0.000000'];
        // Масштаб вычислений выводится из множителя, а не ограничивается фиксированными 16 знаками.
        yield ['1.000000', '0.49999999999999999999999999999', '0.000000'];
        yield ['1.000000', '0.50000000000000000000000000000', '0.000001'];
        yield ['99999999999999999.999999', '0', '0.000000'];
    }

    #[DataProvider('offPeakCases')]
    public function testOffPeak(string $start, string $end, string $ts, bool $expected): void
    {
        self::assertSame($expected, (new OffPeak($start, $end, '0.5'))->contains(Input::instant($ts)));
    }

    public static function offPeakCases(): iterable
    {
        yield ['16:30', '00:30', '2026-09-15T16:29:59.999999Z', false];
        yield ['16:30', '00:30', '2026-09-15T16:30:00Z', true];
        yield ['16:30', '00:30', '2026-09-16T00:00:00Z', true];
        yield ['16:30', '00:30', '2026-09-16T00:29:59.999999Z', true];
        yield ['16:30', '00:30', '2026-09-16T00:30:00Z', false];
        yield ['10:00', '12:00', '2026-09-15T10:00:00Z', true];
        yield ['10:00', '12:00', '2026-09-15T12:00:00Z', false];
        yield ['10:00', '10:00', '2026-09-15T10:00:00Z', false];
    }

    public function testZeroTokensStillHaveRatedZeroCost(): void
    {
        self::assertSame('0.000000', (new CostCalculator())->calculate(
            new TokenUsage(0, 0, 0, 0),
            $this->price(Price::UNIFIED, ['any' => '2.400000']),
            '0.5',
        ));
    }

    #[DataProvider('badDates')]
    public function testInvalidDatesAreNotNormalizedSilently(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Input::instant($value);
    }

    public static function badDates(): iterable
    {
        yield ['2026-02-30T00:00:00Z'];
        yield ['2026-09-15T24:00:00Z'];
        yield ['2026-09-15T00:00:60Z'];
        yield ['2026-09-15T00:00:00+00:00'];
        yield ['2026-09-15'];
    }

    public function testInstantConversionPreservesUtcAndMicroseconds(): void
    {
        $type = new UtcInstantType();
        $platform = new PostgreSQLPlatform();
        $original = new \DateTimeImmutable('2026-09-15T15:00:00.123456+03:00');
        self::assertSame('2026-09-15 12:00:00.123456', $type->convertToDatabaseValue($original, $platform));
        $restored = $type->convertToPHPValue('2026-09-15 12:00:00.123456', $platform);
        self::assertSame('2026-09-15T12:00:00.123456Z', Input::displayInstant($restored));
        self::assertSame('UTC', $restored->getTimezone()->getName());
    }

    public function testNegativeTokensRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TokenUsage(0, -1, 0, 0);
    }

    public function testPriceUpsertIgnoresRateKeyOrderAndLeadingZeros(): void
    {
        $price = $this->price(Price::SPLIT, ['input' => '03.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000']);
        self::assertFalse($price->replace('USD', Price::SPLIT, ['cache_write' => '03.750000', 'output' => '12.000000', 'cache_read' => '0.300000', 'input' => '3.000000']));
    }

    public function testPriceChangesAreComparedAsStringsNotFloatingPointNumbers(): void
    {
        $price = $this->price(Price::UNIFIED, ['any' => '9007199254740992.000001']);
        self::assertTrue($price->replace('USD', Price::UNIFIED, ['any' => '9007199254740992.000002']));
        self::assertFalse($price->replace('USD', Price::UNIFIED, ['any' => '9007199254740992.000002']));
    }

    public function testNumericJsonRatesAreRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->price(Price::UNIFIED, ['any' => 2]);
    }
}
