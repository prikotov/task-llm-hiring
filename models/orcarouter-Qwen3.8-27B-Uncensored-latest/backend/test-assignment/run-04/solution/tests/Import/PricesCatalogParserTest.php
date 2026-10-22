<?php

declare(strict_types=1);

namespace App\Tests\Import;

use App\Import\PricesCatalogParser;
use App\Shared\ImportException;
use PHPUnit\Framework\TestCase;

final class PricesCatalogParserTest extends TestCase
{
    private PricesCatalogParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PricesCatalogParser();
    }

    public function testFixtureCatalog(): void
    {
        $json = (static function (): string {
            return \file_get_contents(__DIR__ . '/../../fixtures/prices.json');
        })();

        $decoded = \json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        $catalog = $this->parser->parse($decoded, PricesCatalogParser::MODE_IMPORT);

        self::assertCount(2, $catalog->providers);
        self::assertSame(3, \array_sum(\array_map('\count', $catalog->modelsByProvider)));
        self::assertCount(4, $catalog->prices);

        $prism = $catalog->providers['prism'];
        self::assertSame('16:30', $prism->offPeak['window_start']);
        self::assertSame('00:30', $prism->offPeak['window_end']);
        self::assertSame('0.5', $prism->offPeak['multiplier']);
        self::assertNull($catalog->providers['sfera']->offPeak);
    }

    public function testUnknownModelRejected(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parse([
            'models' => [
                ['provider' => 'p1', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'p1',
                    'model' => 'm2',
                    'valid_from' => '2026-01-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['any' => '1.000000'],
                ],
            ],
        ], PricesCatalogParser::MODE_IMPORT);
    }

    public function testPerTokenRequiresAllFourRates(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parse([
            'models' => [
                ['provider' => 'p1', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'p1',
                    'model' => 'm1',
                    'valid_from' => '2026-01-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens',
                    'rates' => ['input' => '1.000000'],
                ],
            ],
        ], PricesCatalogParser::MODE_IMPORT);
    }

    public function testUnifiedRequiresAnyRate(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parse([
            'models' => [
                ['provider' => 'p1', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'p1',
                    'model' => 'm1',
                    'valid_from' => '2026-01-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['input' => '1.000000'],
                ],
            ],
        ], PricesCatalogParser::MODE_IMPORT);
    }

    public function testRateMustBeNonNegativeDecimal(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parse([
            'models' => [
                ['provider' => 'p1', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'p1',
                    'model' => 'm1',
                    'valid_from' => '2026-01-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['any' => -0.5],
                ],
            ],
        ], PricesCatalogParser::MODE_IMPORT);
    }
}
