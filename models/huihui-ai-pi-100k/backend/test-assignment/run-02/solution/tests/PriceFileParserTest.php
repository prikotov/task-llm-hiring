<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Prices\PriceFileParser;
use App\Domain\Pricing\PriceUnit;
use App\Exception\InvalidPricesFileException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PriceFileParserTest extends TestCase
{
    private function parser(): PriceFileParser
    {
        return new PriceFileParser();
    }

    public function testValidFile(): void
    {
        $spec = $this->parser()->parse(<<<'JSON'
        {
          "providers": [
            {
              "code": "prism",
              "name": "Prism AI",
              "off_peak": { "window_start": "16:30", "window_end": "00:30", "multiplier": "0.5" }
            },
            { "code": "sfera", "name": "Sfera Cloud", "off_peak": null }
          ],
          "models": [ { "provider": "prism", "code": "lumen-8" } ],
          "prices": [
            {
              "provider": "prism",
              "model": "lumen-8",
              "valid_from": "2026-08-01T00:00:00Z",
              "currency": "USD",
              "unit": "per_1m_tokens",
              "rates": { "input": "3.000000", "output": "12.000000", "cache_read": "0.300000", "cache_write": "3.750000" }
            }
          ]
        }
        JSON);

        self::assertCount(2, $spec->providers);
        self::assertSame('prism', $spec->providers[0]->code);
        self::assertNotNull($spec->providers[0]->offPeak);
        self::assertSame(990, $spec->providers[0]->offPeak->startMinutes);
        self::assertSame(30, $spec->providers[0]->offPeak->endMinutes);
        self::assertSame('0.5', $spec->providers[0]->offPeak->multiplier);
        self::assertNull($spec->providers[1]->offPeak);

        self::assertCount(1, $spec->priceRows);
        $row = $spec->priceRows[0];
        self::assertSame(PriceUnit::Per1mTokens, $row->unit);
        self::assertSame('2026-08-01', $row->validFrom->format('Y-m-d'));
        self::assertSame('3.000000', $row->rates['input']);
    }

    public function testPricesOnlyFileIsAllowed(): void
    {
        // Ретро-коррекция: без секций providers/models.
        $spec = $this->parser()->parse(<<<'JSON'
        {
          "prices": [
            { "provider": "prism", "model": "lumen-8", "valid_from": "2026-08-01T00:00:00Z",
              "currency": "USD", "unit": "per_1m_tokens",
              "rates": { "input": "1", "output": "1", "cache_read": "1", "cache_write": "1" } }
          ]
        }
        JSON);

        self::assertSame([], $spec->providers);
        self::assertSame([], $spec->models);
        self::assertCount(1, $spec->priceRows);
    }

    #[DataProvider("provideInvalidFiles")]
    public function testInvalidFile(string $label, string $json): void
    {
        $this->expectException(InvalidPricesFileException::class);
        $this->parser()->parse($json);
    }

    public static function provideInvalidFiles(): iterable
    {
        yield 'not json' => ['not json', '{oops'];
        yield 'top-level array' => ['top-level array', '[]'];
        yield 'prices missing' => ['prices missing', '{"providers": []}'];
        yield 'prices empty' => ['prices empty', '{"prices": []}'];
        yield 'bad unit' => ['bad unit', self::rowJson('per_token')];
        yield 'rates missing key' => ['rates missing key', self::rowJson('per_1m_tokens', '{"output": "1"}')];
        yield 'extra rate key' => ['extra rate key', self::rowJson('per_1m_tokens', '{"input": "1", "output": "1", "cache_read": "1", "cache_write": "1", "any": "1"}')];
        yield 'non-string rate' => ['non-string rate', self::rowJson('per_1m_tokens', '{"input": 3, "output": "1", "cache_read": "0", "cache_write": "0"}')];
        yield 'negative rate' => ['negative rate', self::rowJson('per_1m_tokens', '{"input": "-1", "output": "1", "cache_read": "0", "cache_write": "0"}')];
        yield 'bad valid_from (offset zone)' => ['bad valid_from', self::rowJson('per_1m_tokens', '{"input": "1", "output": "1", "cache_read": "0", "cache_write": "0"}', '"2026-08-01T00:00:00+00:00"')];
        yield 'bad valid_from (no Z)' => ['bad valid_from', self::rowJson('per_1m_tokens', '{"input": "1", "output": "1", "cache_read": "0", "cache_write": "0"}', '"2026-08-01 00:00:00"')];
        yield 'off_peak bad window' => ['off_peak bad window', json_encode(['prices' => [], 'providers' => [[
            'code' => 'p', 'name' => 'P',
            'off_peak' => ['window_start' => '25:00', 'window_end' => '26:00', 'multiplier' => '0.5'],
        ]]] ?: '')];
        yield 'off_peak zero multiplier' => ['off_peak zero multiplier', json_encode(['prices' => [], 'providers' => [[
            'code' => 'p', 'name' => 'P',
            'off_peak' => ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0'],
        ]]] ?: '')];
    }

    private static function rowJson(string $unit, string $ratesJson = null, string $validFrom = '"2026-08-01T00:00:00Z"'): string
    {
        $rates = $ratesJson !== null ? $ratesJson : '{"input": "1", "output": "1", "cache_read": "0", "cache_write": "0"}';

        return '{"prices": [ {'
            .'"provider": "prism", "model": "lumen-8", "valid_from": '.$validFrom
            .', "currency": "USD", "unit": "'.$unit.'", "rates": '.$rates
            .'} ]}';
    }

    public function testUnifiedRates(): void
    {
        $json = '{"prices": [ {'
            .'"provider": "sfera", "model": "gyro-pro", "valid_from": "2026-08-01T00:00:00Z"'
            .', "currency": "USD", "unit": "per_1m_tokens_unified", "rates": {"any": "2.400000"}'
            .'} ]}';
        $spec = $this->parser()->parse($json);

        self::assertSame(PriceUnit::Per1mTokensUnified, $spec->priceRows[0]->unit);
        self::assertSame('2.400000', $spec->priceRows[0]->rates['any']);
    }
}
