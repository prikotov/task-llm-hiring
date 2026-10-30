<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\CatalogParser;
use App\Domain\PriceUnit;
use App\Exception\ImportException;
use PHPUnit\Framework\TestCase;

final class CatalogParserTest extends TestCase
{
    private CatalogParser $parser;

    protected function setUp(): void
    {
        $this->parser = new CatalogParser();
    }

    private const VALID = <<<'JSON'
    {
      "providers": [
        {
          "code": "prism",
          "name": "Prism AI",
          "off_peak": { "window_start": "16:30", "window_end": "00:30", "multiplier": "0.5" }
        },
        { "code": "sfera", "name": "Sfera Cloud", "off_peak": null }
      ],
      "models": [
        { "provider": "prism", "code": "lumen-8" },
        { "provider": "sfera", "code": "gyro-pro" }
      ],
      "prices": [
        {
          "provider": "prism",
          "model": "lumen-8",
          "valid_from": "2026-08-01T00:00:00Z",
          "currency": "USD",
          "unit": "per_1m_tokens",
          "rates": { "input": "3.000000", "output": "12.000000", "cache_read": "0.300000", "cache_write": "3.750000" }
        },
        {
          "provider": "sfera",
          "model": "gyro-pro",
          "valid_from": "2026-08-01T00:00:00Z",
          "currency": "USD",
          "unit": "per_1m_tokens_unified",
          "rates": { "any": "2.400000" }
        }
      ]
    }
    JSON;

    public function testValidFileIsParsed(): void
    {
        $catalog = $this->parser->parse(self::VALID);

        self::assertCount(2, $catalog->providers);
        self::assertSame('prism', $catalog->providers[0]->code);
        self::assertSame(['start' => '16:30', 'end' => '00:30', 'multiplier' => '0.5'], $catalog->providers[0]->offPeak);
        self::assertNull($catalog->providers[1]->offPeak);
        self::assertCount(2, $catalog->models);
        self::assertCount(2, $catalog->prices);
        self::assertSame(PriceUnit::Per1mTokens, $catalog->prices[0]->unit);
        self::assertSame('3.000000', $catalog->prices[0]->rates['input']);
        self::assertSame(PriceUnit::Per1mTokensUnified, $catalog->prices[1]->unit);
        self::assertSame('2026-08-01 00:00:00', $catalog->prices[0]->validFrom->format('Y-m-d H:i:s'));
    }

    public function testMissingSectionsMeanEmpty(): void
    {
        $catalog = $this->parser->parse('{}');

        self::assertSame([], $catalog->providers);
        self::assertSame([], $catalog->models);
        self::assertSame([], $catalog->prices);
    }

    public function testNumericRateIsRejected(): void
    {
        $json = str_replace('"input": "3.000000"', '"input": 3', self::VALID);

        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/rates\.input/');

        $this->parser->parse($json);
    }

    public function testUnknownRateKeyIsRejected(): void
    {
        $json = str_replace(
            '"cache_write": "3.750000" }',
            '"cache_write": "3.750000", "prompt": "1.0" }',
            self::VALID,
        );

        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/prompt/');

        $this->parser->parse($json);
    }

    public function testNonStringTimestampIsRejected(): void
    {
        $json = str_replace('2026-08-01T00:00:00Z', '2026-08-01', self::VALID);

        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/valid_from/');

        $this->parser->parse($json);
    }

    public function testNonUtcTimestampIsRejected(): void
    {
        $json = str_replace('2026-08-01T00:00:00Z', '2026-08-01T00:00:00+02:00', self::VALID);

        $this->expectException(ImportException::class);

        $this->parser->parse($json);
    }

    public function testInvalidOffPeakWindowIsRejected(): void
    {
        $json = str_replace('"window_start": "16:30"', '"window_start": "pm six thirty"', self::VALID);

        $this->expectException(ImportException::class);

        $this->parser->parse($json);
    }

    public function testNotAnObjectRootIsRejected(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parse('[1, 2, 3]');
    }

    public function testBrokenJsonIsRejected(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parse('{ "providers": [');
    }
}
