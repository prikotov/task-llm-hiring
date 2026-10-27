<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Unit;
use App\Ingest\IngestError;
use App\Ingest\PriceFileParser;
use PHPUnit\Framework\TestCase;

final class PriceFileParserTest extends TestCase
{
    private PriceFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PriceFileParser();
    }

    public function testParsesFixtureCatalog(): void
    {
        $catalog = $this->parser->parse(file_get_contents(__DIR__.'/../../fixtures/prices.json'));

        self::assertCount(2, $catalog['providers']);
        self::assertCount(3, $catalog['models']);
        self::assertCount(4, $catalog['prices']);
        self::assertSame(Unit::PER_1M_TOKENS, $catalog['prices'][0]->unit);
        self::assertSame('0.5', $catalog['providers'][0]->offPeakMultiplier);
    }

    public function testRejectsNonObjectRoot(): void
    {
        try {
            $this->parser->parse('[1, 2]');
            self::fail('expected exception');
        } catch (IngestError $e) {
            self::assertStringContainsString('объектом JSON', $e->getMessage());
        }
    }

    public function testRejectsUnknownUnit(): void
    {
        $json = (static fn (): string => json_encode([
            'providers' => [],
            'models' => [],
            'prices' => [[
                'provider' => 'p',
                'model' => 'm',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_kilometer',
                'rates' => ['any' => '1.000000'],
            ]],
        ], JSON_THROW_ON_ERROR))();

        try {
            $this->parser->parse($json);
            self::fail('expected exception');
        } catch (IngestError $e) {
            self::assertStringContainsString('unit', $e->getMessage());
        }
    }

    public function testPerMillionRequiresAllFourRates(): void
    {
        $json = (static fn (): string => json_encode([
            'prices' => [[
                'provider' => 'p',
                'model' => 'm',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '1.000000', 'output' => '2.000000', 'any' => '9.000000'],
            ]],
        ], JSON_THROW_ON_ERROR))();

        try {
            $this->parser->parse($json);
            self::fail('expected exception');
        } catch (IngestError $e) {
            self::assertStringContainsString('cache_read', $e->getMessage());
        }
    }

    public function testUnifiedRequiresOnlyAnyRate(): void
    {
        $catalog = $this->parser->parse((static fn (): string => json_encode([
            'prices' => [[
                'provider' => 'p',
                'model' => 'm',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens_unified',
                'rates' => ['any' => '1.500000'],
            ]],
        ], JSON_THROW_ON_ERROR))(
        ));

        self::assertCount(1, $catalog['prices']);
        self::assertSame(['any' => '1.500000'], $catalog['prices'][0]->rates);
    }

    public function testRejectsOffPeakBadWindow(): void
    {
        $json = (static fn (): string => json_encode([
            'providers' => [[
                'code' => 'p',
                'name' => 'P',
                'off_peak' => ['window_start' => '25:00', 'window_end' => '01:00', 'multiplier' => '0.5'],
            ]],
        ], JSON_THROW_ON_ERROR))(
        );

        try {
            $this->parser->parse($json);
            self::fail('expected exception');
        } catch (IngestError $e) {
            self::assertStringContainsString('window_start', $e->getMessage());
        }
    }

    public function testUsageLineParsing(): void
    {
        $line = '{"provider":"p","provider_request_id":"r1","model":"m","user":"u","ts":"2026-09-15T11:00:00Z","tokens_input":10,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":5}';
        $def = $this->parser->parseUsageLine($line);

        self::assertSame('p', $def->providerCode);
        self::assertSame('r1', $def->providerRequestId);
        self::assertSame(5, $def->tokensCacheWrite);
        self::assertSame('+00:00', $def->tsUtc->format('P'));
    }

    public function testUsageLineRejectsNegativeTokens(): void
    {
        try {
            $this->parser->parseUsageLine('{"provider":"p","provider_request_id":"r1","model":"m","user":"u","ts":"2026-09-15T11:00:00Z","tokens_input":-1,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}');
            self::fail('expected exception');
        } catch (IngestError $e) {
            self::assertStringContainsString('tokens_input', $e->getMessage());
        }
    }

    public function testUsageLineMissingTokensDefaultToZero(): void
    {
        // По заданию любое количество токенов может быть нулевым;
        // провайдеры, не передающие некоторые типы токенов, остаются совместимы.
        $def = $this->parser->parseUsageLine('{"provider":"p","provider_request_id":"r1","model":"m","user":"u","ts":"2026-09-15T11:00:00Z"}');

        self::assertSame(0, $def->tokensInput);
        self::assertSame(0, $def->tokensOutput);
        self::assertSame(0, $def->tokensCacheRead);
        self::assertSame(0, $def->tokensCacheWrite);
    }
}
