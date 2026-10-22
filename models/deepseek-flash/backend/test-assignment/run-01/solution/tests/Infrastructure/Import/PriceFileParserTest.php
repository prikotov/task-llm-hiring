<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Import;

use App\Domain\Pricing\PriceUnit;
use App\Infrastructure\Import\PriceFileParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class PriceFileParserTest extends TestCase
{
    private PriceFileParser $parser;

    /** @var list<string> */
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        $this->parser = new PriceFileParser();
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            @unlink($file);
        }
    }

    public function testParsesFixture(): void
    {
        $data = $this->parser->parse(dirname(__DIR__, 3) . '/fixtures/prices.json');

        self::assertCount(2, $data->providers);
        self::assertCount(3, $data->models);
        self::assertCount(4, $data->prices);

        $prism = $data->providers[0];
        self::assertSame('prism', $prism->code);
        self::assertNotNull($prism->offPeak);
        self::assertTrue($prism->offPeak->contains(new \DateTimeImmutable('2026-09-15T17:00:00Z')));

        $firstPrice = $data->prices[0];
        self::assertSame('prism', $firstPrice->providerCode);
        self::assertSame('lumen-8', $firstPrice->modelCode);
        self::assertSame(PriceUnit::PerMillionTokens, $firstPrice->rateCard->unit);
        self::assertSame('3', (string) $firstPrice->rateCard->input);
    }

    public function testAcceptsCorrectionFileWithoutProviderAndModelSections(): void
    {
        $file = $this->temporaryFile(<<<'JSON'
            {
              "prices": [
                {
                  "provider": "prism",
                  "model": "lumen-8",
                  "valid_from": "2026-09-15T12:00:00Z",
                  "currency": "USD",
                  "unit": "per_1m_tokens",
                  "rates": {
                    "input": "2.000000",
                    "output": "8.000000",
                    "cache_read": "0.150000",
                    "cache_write": "1.875000"
                  }
                }
              ]
            }
            JSON);

        $data = $this->parser->parse($file);

        self::assertSame([], $data->providers);
        self::assertSame([], $data->models);
        self::assertCount(1, $data->prices);
    }

    public function testRejectsInvalidJson(): void
    {
        $file = $this->temporaryFile('{"providers": [');

        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse($file);
    }

    public function testRejectsNumericRateBecauseMoneyMustBeDecimalString(): void
    {
        $file = $this->temporaryFile(<<<'JSON'
            {
              "prices": [
                {
                  "provider": "prism",
                  "model": "lumen-8",
                  "valid_from": "2026-09-15T12:00:00Z",
                  "currency": "USD",
                  "unit": "per_1m_tokens_unified",
                  "rates": { "any": 2.4 }
                }
              ]
            }
            JSON);

        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse($file);
    }

    private function temporaryFile(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'billing-prices-');
        if ($file === false) {
            self::fail('Cannot create a temporary file.');
        }

        file_put_contents($file, $contents);
        $this->temporaryFiles[] = $file;

        return $file;
    }
}
