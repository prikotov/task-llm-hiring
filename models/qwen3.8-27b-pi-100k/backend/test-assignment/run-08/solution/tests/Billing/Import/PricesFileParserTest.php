<?php

declare(strict_types=1);

namespace App\Tests\Billing\Import;

use App\Billing\Import\ImportException;
use App\Billing\Import\PricesFileParser;
use PHPUnit\Framework\TestCase;

final class PricesFileParserTest extends TestCase
{
    private PricesFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PricesFileParser();
    }

    private const VALID = <<<'JSON'
    {
      "providers": [
        { "code": "prism", "name": "Prism AI",
          "off_peak": { "window_start": "16:30", "window_end": "00:30", "multiplier": "0.5" } },
        { "code": "sfera", "name": "Sfera Cloud", "off_peak": null }
      ],
      "models": [
        { "provider": "prism", "code": "lumen-8" },
        { "provider": "sfera", "code": "gyro-pro" }
      ],
      "prices": [
        { "provider": "prism", "model": "lumen-8", "valid_from": "2026-08-01T00:00:00Z",
          "currency": "USD", "unit": "per_1m_tokens",
          "rates": { "input": "3.000000", "output": "12.000000", "cache_read": "0.300000", "cache_write": "3.750000" } },
        { "provider": "sfera", "model": "gyro-pro", "valid_from": "2026-08-01T00:00:00Z",
          "currency": "USD", "unit": "per_1m_tokens_unified", "rates": { "any": "2.400000" } }
      ]
    }
    JSON;

    public function testParsesValidFile(): void
    {
        $file = $this->parser->parse(self::VALID);

        self::assertCount(2, $file->providers);
        self::assertSame('prism', $file->providers[0]->code);
        self::assertNotNull($file->providers[0]->offPeak);
        self::assertSame(16 * 60 + 30, $file->providers[0]->offPeak->windowStartMinutes);
        self::assertSame(0 * 60 + 30, $file->providers[0]->offPeak->windowEndMinutes);
        self::assertNull($file->providers[1]->offPeak);

        self::assertCount(2, $file->models);
        self::assertCount(2, $file->priceRows);
        self::assertSame('USD', $file->priceRows[0]->currency);
        self::assertSame('2026-08-01T00:00:00+00:00', $file->priceRows[0]->validFrom->format('Y-m-d\TH:i:sP'));
        self::assertSame('3.000000', $file->priceRows[0]->price->rate('input')->toFixed(6));
        self::assertSame('2.400000', $file->priceRows[1]->price->rate('any')->toFixed(6));
    }

    public function testSectionsAreOptional(): void
    {
        $file = $this->parser->parse('{"prices": []}');
        self::assertSame([], $file->providers);
        self::assertSame([], $file->models);
        self::assertSame([], $file->priceRows);
    }

    private function expectParseError(string $json): void
    {
        try {
            $this->parser->parse($json);
            self::fail('ожидался ImportException');
        } catch (ImportException) {
            self::assertTrue(true);
        }
    }

    public function testInvalidJsonRejected(): void
    {
        $this->expectParseError('{ это не json');
    }

    public function testMalformedRateRejected(): void
    {
        $json = str_replace('"input": "3.000000"', '"input": "три рубля"', self::VALID);
        $this->expectParseError($json);
    }

    public function testNegativeRateRejected(): void
    {
        $json = str_replace('"input": "3.000000"', '"input": "-3.000000"', self::VALID);
        $this->expectParseError($json);
    }

    public function testMissingRequiredRateRejected(): void
    {
        $json = str_replace(' "cache_read": "0.300000",', '', self::VALID);
        $this->expectParseError($json);
    }

    public function testUnknownUnitRejected(): void
    {
        $json = str_replace('"per_1m_tokens_unified"', '"per_1k_tokens"', self::VALID);
        $this->expectParseError($json);
    }

    public function testBadTimestampRejected(): void
    {
        $json = str_replace('"2026-08-01T00:00:00Z"', '"2026-13-45"', self::VALID, $count);
        self::assertGreaterThan(0, $count);
        $this->expectParseError($json);
    }

    public function testDuplicateProviderCodeRejected(): void
    {
        $json = str_replace('"code": "sfera"', '"code": "prism"', self::VALID);
        $this->expectParseError($json);
    }

    public function testBadMultiplierRejected(): void
    {
        $json = str_replace('"multiplier": "0.5"', '"multiplier": "discount"', self::VALID);
        $this->expectParseError($json);
    }
}
