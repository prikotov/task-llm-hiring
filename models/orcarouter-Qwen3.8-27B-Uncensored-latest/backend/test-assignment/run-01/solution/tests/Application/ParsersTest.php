<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Application\Parsers\PricesFile;
use App\Application\Parsers\Scalar;
use App\Application\Parsers\UsageEventLine;
use App\Domain\Exception\ImportException;
use PHPUnit\Framework\TestCase;

final class ParsersTest extends TestCase
{
    public function testIsoUtcRequiresUtc(): void
    {
        self::assertSame(
            '2026-09-15 11:00:00',
            Scalar::isoUtc('2026-09-15T11:00:00Z', 't')->format('Y-m-d H:i:s'),
        );
        self::assertSame(
            '2026-09-15 11:00:00',
            Scalar::isoUtc('2026-09-15T11:00:00+00:00', 't')->format('Y-m-d H:i:s'),
        );

        // явно ненулевое смещение — не UTC
        $this->expectException(ImportException::class);
        Scalar::isoUtc('2026-09-15T14:00:00+03:00', 't');
    }

    public function testTimeOfDayValidation(): void
    {
        self::assertSame('00:30', Scalar::timeOfDay('00:30', 't'));
        self::assertSame('23:59', Scalar::timeOfDay('23:59', 't'));

        foreach (['24:00', '1:30', '12:60', '1234', 'abc'] as $bad) {
            try {
                Scalar::timeOfDay($bad, 't');
                self::fail($bad.' должен быть отклонён');
            } catch (ImportException) {
            }
        }
    }

    public function testDecimalAllowsUpToSixDecimals(): void
    {
        self::assertSame('2.500000', Scalar::decimal('2.500000', 't'));
        self::assertSame('3', Scalar::decimal('3', 't'));
        self::assertSame('0', Scalar::decimal('0', 't'));

        foreach (['2.5000001', '-1', '1.2.3', ''] as $bad) {
            try {
                Scalar::decimal($bad, 't');
                self::fail($bad.' должен быть отклонён');
            } catch (ImportException) {
            }
        }
    }

    public function testTokenCountRejectsNegativeAndFloats(): void
    {
        self::assertSame(1_200_000, Scalar::tokenCount(1_200_000, 'tokens_input', 't'));

        foreach ([-1, 1.5, 'x', null, true] as $bad) {
            try {
                Scalar::tokenCount($bad, 'tokens_input', 't');
                self::fail(var_export($bad, true).' должен быть отклонён');
            } catch (ImportException) {
            }
        }
    }

    public function testUsageEventLineParsesAllFields(): void
    {
        $line = '{"provider":"prism","provider_request_id":"e1","model":"lumen-8","user":"u","ts":"2026-09-15T11:00:00Z","tokens_input":1200000,"tokens_output":800000,"tokens_cache_read":2500000,"tokens_cache_write":83333}';
        $data = UsageEventLine::fromJsonLine($line, 1);
        self::assertSame('prism', $data['provider']);
        self::assertSame('e1', $data['provider_request_id']);
        self::assertSame('lumen-8', $data['model']);
        self::assertSame(
            ['input' => 1_200_000, 'output' => 800_000, 'cache_read' => 2_500_000, 'cache_write' => 83_333],
            $data['tokens'],
        );
        self::assertInstanceOf(\DateTimeImmutable::class, $data['ts']);
    }

    public function testUsageEventLineRejectsBadLine(): void
    {
        $this->expectException(ImportException::class);
        UsageEventLine::fromJsonLine('{"provider":"a"}', 1);
    }

    public function testPricesFileParserShape(): void
    {
        $json = '{"providers":[{"code":"p","name":"P","off_peak":{"window_start":"16:30","window_end":"00:30","multiplier":"0.5"}}],'
            .'"models":[{"provider":"p","code":"m"}],'
            .'"prices":[{"provider":"p","model":"m","valid_from":"2026-08-01T00:00:00Z","currency":"USD","unit":"per_1m_tokens","rates":{"input":"1","output":"2","cache_read":"0.1","cache_write":"0.2"}}]}';
        $data = PricesFile::parse($json, true);
        self::assertSame('p', $data['providers'][0]['code']);
        self::assertSame('0.5', $data['providers'][0]['offpeak']['multiplier']);
        self::assertInstanceOf(\DateTimeImmutable::class, $data['prices'][0]['valid_from']);
        self::assertSame('per_1m_tokens', $data['prices'][0]['unit']);
        self::assertSame('0.1', $data['prices'][0]['rates']['cache_read']);
    }

    public function testPricesFileRejectsUnknownUnit(): void
    {
        $json = '{"providers":[],"models":[],"prices":[{"provider":"p","model":"m","valid_from":"2026-08-01T00:00:00Z","currency":"USD","unit":"per_kilo_tokens","rates":{"input":"1"}}]}';
        $this->expectException(ImportException::class);
        PricesFile::parse($json, true);
    }

    public function testPricesFileRequiresRatesForUnit(): void
    {
        // per_1m_tokens без cache_read/cache_write
        $json = '{"providers":[],"models":[],"prices":[{"provider":"p","model":"m","valid_from":"2026-08-01T00:00:00Z","currency":"USD","unit":"per_1m_tokens","rates":{"input":"1","output":"2"}}]}';
        $this->expectException(ImportException::class);
        PricesFile::parse($json, true);
    }

    public function testPricesFileEmptyPricesInvalidWhenRequired(): void
    {
        $this->expectException(ImportException::class);
        PricesFile::parse('{"providers":[{"code":"p","name":"P"}],"models":[],"prices":[]}', true);
    }

    public function testUnifiedRatesAcceptAnyKeyOnly(): void
    {
        $ok = '{"providers":[],"models":[]'
            .',"prices":[{"provider":"p","model":"m","valid_from":"2026-08-01T00:00:00Z","currency":"USD","unit":"per_1m_tokens_unified","rates":{"any":"2.4"}}]}';
        self::assertSame('2.4', PricesFile::parse($ok, true)['prices'][0]['rates']['any']);

        $bad = '{"providers":[],"models":[]'
            .',"prices":[{"provider":"p","model":"m","valid_from":"2026-08-01T00:00:00Z","currency":"USD","unit":"per_1m_tokens_unified","rates":{"input":"2.4"}}]}';
        $this->expectException(ImportException::class);
        PricesFile::parse($bad, true);
    }
}
