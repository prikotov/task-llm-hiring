<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\PricesFileParser;
use App\Domain\Exception\BillingException;
use PHPUnit\Framework\TestCase;

final class PricesFileParserTest extends TestCase
{
    private PricesFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PricesFileParser();
    }

    public function testParsesFixtureFile(): void
    {
        $data = $this->parser->parseFile('fixtures/prices.json');

        self::assertCount(2, $data->providers);
        self::assertSame('prism', $data->providers[0]->code);
        self::assertSame('16:30', $data->providers[0]->windowStart);
        self::assertSame('00:30', $data->providers[0]->windowEnd);
        self::assertSame('0.5', $data->providers[0]->multiplier);
        self::assertNull($data->providers[1]->windowStart); // sfera — без скидки
        self::assertCount(3, $data->models);
        self::assertCount(4, $data->prices);

        $first = $data->prices[0];
        self::assertSame('2026-08-01T00:00:00+00:00', $first->validFrom->format('Y-m-d\TH:i:sP'));
        self::assertSame('per_1m_tokens', $first->unit);
        self::assertSame('3.000000', $first->rates['input']);
    }

    public function testMissingSectionsMeanEmptyLists(): void
    {
        // Файл коррекции цен: только секция prices.
        $data = $this->parser->parse('{"prices": []}');

        self::assertSame(0, \count($data->providers));
        self::assertSame(0, \count($data->models));
        self::assertSame(0, \count($data->prices));
    }

    public function testNumericJsonRatesAreRejected(): void
    {
        $json = '{"prices": [
            {"provider": "p", "model": "m", "valid_from": "2026-08-01T00:00:00Z",
             "currency": "USD", "unit": "per_1m_tokens_unified", "rates": {"any": 2.4}}
        ]}';

        $this->expectException(BillingException::class);
        $this->expectExceptionMessageMatches('/any/');
        $this->parser->parse($json);
    }

    public function testMissingRateKeyIsRejected(): void
    {
        $json = '{"prices": [
            {"provider": "p", "model": "m", "valid_from": "2026-08-01T00:00:00Z",
             "currency": "USD", "unit": "per_1m_tokens",
             "rates": {"input": "1", "output": "1", "cache_read": "1"}}
        ]}';

        $this->expectException(BillingException::class);
        $this->expectExceptionMessageMatches('/cache_write/');
        $this->parser->parse($json);
    }

    public function testUnknownUnitIsRejected(): void
    {
        $json = '{"prices": [
            {"provider": "p", "model": "m", "valid_from": "2026-08-01T00:00:00Z",
             "currency": "USD", "unit": "per_1k_tokens", "rates": {"any": "2.4"}}
        ]}';

        $this->expectException(BillingException::class);
        $this->parser->parse($json);
    }

    public function testNegativeRateIsRejected(): void
    {
        $json = '{"prices": [
            {"provider": "p", "model": "m", "valid_from": "2026-08-01T00:00:00Z",
             "currency": "USD", "unit": "per_1m_tokens_unified", "rates": {"any": "-1"}}
        ]}';

        $this->expectException(BillingException::class);
        $this->parser->parse($json);
    }

    public function testInvalidOffPeakWindowIsRejected(): void
    {
        $json = '{"providers": [
            {"code": "p", "name": "P", "off_peak": {"window_start": "25:00", "window_end": "00:30", "multiplier": "0.5"}}
        ]}';

        $this->expectException(BillingException::class);
        $this->parser->parse($json);
    }
}
