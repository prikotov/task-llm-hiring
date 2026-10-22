<?php

declare(strict_types=1);

namespace App\Tests;

use App\Import\ImportException;
use App\Import\PricesParser;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PricesParserTest extends TestCase
{
    private function validImportJson(): string
    {
        return <<<'JSON'
        {
          "providers": [
            { "code": "prism", "name": "Prism", "off_peak": { "window_start": "16:30", "window_end": "00:30", "multiplier": "0.5" } },
            { "code": "sfera", "name": "Sfera", "off_peak": null }
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
        JSON;
    }

    public function testValidImportFile(): void
    {
        $payload = (new PricesParser())->parseImport($this->validImportJson());

        self::assertCount(2, $payload->providers);
        self::assertSame('prism', $payload->providers[0]->code);
        self::assertSame(990, $payload->providers[0]->offPeakStartMinute);
        self::assertSame(30, $payload->providers[0]->offPeakEndMinute);
        self::assertSame('0.5', $payload->providers[0]->offPeakMultiplier);
        self::assertTrue($payload->providers[0]->offPeakTouched);
        self::assertTrue($payload->providers[1]->offPeakTouched, 'Явный null в файле импорта — «оф-пика нет»');
        self::assertNull($payload->providers[1]->offPeakMultiplier);
        self::assertCount(1, $payload->models);
        self::assertCount(1, $payload->priceRows);
        self::assertSame('2026-08-01T00:00:00+00:00', $payload->priceRows[0]->validFrom->format(DateTimeImmutable::ATOM));
    }

    public function testInvalidOffPeakTimeIsRejected(): void
    {
        $json = str_replace('16:30', '25:99', $this->validImportJson());

        $this->expectException(ImportException::class);
        (new PricesParser())->parseImport($json);
    }

    public function testMissingPricesSectionInImportIsRejected(): void
    {
        $json = str_replace('"prices": [', '"prices": []', $this->validImportJson());

        $this->expectException(ImportException::class);
        (new PricesParser())->parseImport($json);
    }

    public function testUnknownRateKeyIsRejected(): void
    {
        $json = str_replace('"cache_write": "3.750000"', '"cache": "3.750000"', $this->validImportJson());

        $this->expectException(ImportException::class);
        (new PricesParser())->parseImport($json);
    }

    public function testNonDecimalRateIsRejected(): void
    {
        $json = str_replace('"input": "3.000000"', '"input": 3.5', $this->validImportJson());

        $this->expectException(ImportException::class);
        (new PricesParser())->parseImport($json);
    }

    public function testRepriceMayOmitProvidersAndModels(): void
    {
        $json = '{"prices": [ { "provider": "prism", "model": "lumen-8", "valid_from": "2026-07-01T00:00:00Z", "currency": "USD", "unit": "per_1m_tokens", "rates": { "input": "1", "output": "1", "cache_read": "1", "cache_write": "1" } } ]}';
        $payload = (new PricesParser())->parseReprice($json);

        self::assertCount(1, $payload->priceRows);
        self::assertSame([], $payload->providers);
        self::assertSame([], $payload->models);
    }

    public function testRepriceWithoutPricesIsRejected(): void
    {
        $json = '{"providers": [ { "code": "prism", "name": "P" } ]}';

        $this->expectException(ImportException::class);
        (new PricesParser())->parseReprice($json);
    }

    public function testRepriceProviderWithoutOffPeakLeavesStoredValueUntouched(): void
    {
        $json = '{"providers": [ { "code": "prism", "name": "Prism" } ], "prices": [ { "provider": "prism", "model": "lumen-8", "valid_from": "2026-07-01T00:00:00Z", "currency": "USD", "unit": "per_1m_tokens", "rates": { "input": "1", "output": "1", "cache_read": "1", "cache_write": "1" } } ]}';

        $payload = (new PricesParser())->parseReprice($json);

        self::assertFalse($payload->providers[0]->offPeakTouched, 'В файле корректировки отсутствующее off_peak не должно сбрасывать сохранённое окно');
    }
}
