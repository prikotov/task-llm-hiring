<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Exception\FileFormatException;
use App\Application\Pricing\PriceFileParser;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\TokenKind;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PriceFileParserTest extends TestCase
{
    public function testValidDocumentIsParsed(): void
    {
        $doc = PriceFileParser::fromJson(self::validJson());

        self::assertCount(2, $doc->providers);
        self::assertCount(2, $doc->models);
        self::assertCount(3, $doc->priceRows);

        $provider = $doc->providers[0];
        self::assertSame('prism', $provider->code);
        self::assertNotNull($provider->offPeak);
        self::assertSame(990, $provider->offPeak->startMinutes());
        self::assertSame(30, $provider->offPeak->endMinutes());
        self::assertSame('0.5', $provider->offPeak->multiplier());

        self::assertSame('prism', $doc->models[0]->providerCode);
        self::assertSame('lumen-8', $doc->models[0]->code);

        $unified = $doc->priceRows[1];
        self::assertSame(PriceUnit::Per1mTokensUnified, $unified->unit);
        self::assertSame('2.400000', $unified->rates[TokenKind::Any->value]);
        self::assertSame('USD', $unified->currency); // по умолчанию

        // Смещение +02:00 нормализуется в UTC.
        self::assertSame(
            '2026-09-15T10:00:00+00:00',
            $doc->priceRows[2]->validFrom->format('Y-m-d\TH:i:sP'),
        );
    }

    public function testMinimalCorrectionDocument(): void
    {
        // Только строки цен — легальный сценарий коррекции (репрайс).
        $doc = PriceFileParser::fromJson(self::validJson('prices only'));

        self::assertSame([], $doc->providers);
        self::assertSame([], $doc->models);
        self::assertCount(1, $doc->priceRows);
    }

    public function testUnknownUnitIsRejected(): void
    {
        $json = str_replace('"unit": "per_1m_tokens",', '"unit": "per_token",', self::validJson());
        self::assertParseFails($json, 'unit');
    }

    public function testMissingRateKindIsRejected(): void
    {
        $json = str_replace('"output": "12.000000", ', '', self::validJson());
        self::assertParseFails($json, 'output');
    }

    public function testNegativeRateIsRejected(): void
    {
        $json = str_replace('"input": "3.000000"', '"input": "-3.0"', self::validJson());
        self::assertParseFails($json);
    }

    public function testNonNumericRateIsRejected(): void
    {
        $json = str_replace('"input": "3.000000"', '"input": "три"', self::validJson());
        self::assertParseFails($json);
    }

    public function testDuplicateProviderCodesAreRejected(): void
    {
        $json = str_replace(
            '"off_peak": null }',
            '"off_peak": null }, { "code": "prism", "name": "Дубль" }',
            self::validJson(),
        );
        self::assertParseFails($json, 'провайдера');
    }

    public function testDuplicateModelsAreRejected(): void
    {
        $json = str_replace(
            '{ "provider": "prism", "code": "lumen-8" }',
            '{ "provider": "prism", "code": "lumen-8" }, { "provider": "prism", "code": "lumen-8" }',
            self::validJson(),
        );
        self::assertParseFails($json, 'несколько раз');
    }

    public function testBadWindowTimeIsRejected(): void
    {
        $json = str_replace('"window_start": "16:30"', '"window_start": "16:30:00"', self::validJson());
        self::assertParseFails($json, 'window_start');
    }

    public function testZeroMultiplierIsRejected(): void
    {
        $json = str_replace('"multiplier": "0.5"', '"multiplier": "0"', self::validJson());
        self::assertParseFails($json, 'multiplier');
    }

    public function testMissingValidFromIsRejected(): void
    {
        $json = str_replace('"valid_from": "2026-08-01T00:00:00Z",', '', self::validJson());
        self::assertParseFails($json, 'valid_from');
    }

    public function testBadValidFromFormatIsRejected(): void
    {
        $json = str_replace('"valid_from": "2026-08-01T00:00:00Z"', '"valid_from": "01.08.2026"', self::validJson());
        self::assertParseFails($json, 'valid_from');
    }

    public function testEmptyPriceRowsDocumentIsAllowed(): void
    {
        $doc = PriceFileParser::fromJson('{"prices": []}');
        self::assertSame([], $doc->priceRows);
    }

    private static function assertParseFails(string $json, ?string $inMessage = null): void
    {
        $threw = false;
        try {
            PriceFileParser::fromJson($json);
        } catch (FileFormatException $e) {
            $threw = true;
            if ($inMessage !== null) {
                self::assertStringContainsString($inMessage, $e->getMessage());
            }
        }
        self::assertTrue($threw, 'Ожидалось FileFormatException');
    }

    private static function validJson(string $shape = 'full'): string
    {
        $providers = '[ { "code": "prism", "name": "Prism AI", "off_peak": { "window_start": "16:30", "window_end": "00:30", "multiplier": "0.5" } }, { "code": "sfera", "name": "Sfera Cloud", "off_peak": null } ]';
        $models = '[ { "provider": "prism", "code": "lumen-8" }, { "provider": "sfera", "code": "gyro-pro" } ]';

        $rows = [
            '{ "provider": "prism", "model": "lumen-8", "valid_from": "2026-08-01T00:00:00Z", "currency": "USD", "unit": "per_1m_tokens", "rates": { "input": "3.000000", "output": "12.000000", "cache_read": "0.300000", "cache_write": "3.750000" } }',
            '{ "provider": "sfera", "model": "gyro-pro", "valid_from": "2026-08-01T00:00:00Z", "unit": "per_1m_tokens_unified", "rates": { "any": "2.400000" } }',
            '{ "provider": "prism", "model": "lumen-8", "valid_from": "2026-09-15T12:00:00+02:00", "currency": "USD", "unit": "per_1m_tokens", "rates": { "input": "1.500000", "output": "8.000000", "cache_read": "0.150000", "cache_write": "1.875000" } }',
        ];

        if ($shape === 'prices only') {
            return '{"prices": [' . $rows[0] . ']}';
        }

        return '{"providers": '.$providers.', "models": '.$models.', "prices": [' . implode(', ', $rows) . ']}';
    }
}
