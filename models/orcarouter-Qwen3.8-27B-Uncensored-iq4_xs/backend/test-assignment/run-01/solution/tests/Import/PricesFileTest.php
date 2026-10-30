<?php

declare(strict_types=1);

namespace App\Tests\Import;

use App\Exception\ImportException;
use App\Import\ParsedProvider;
use App\Import\PricesFile;
use PHPUnit\Framework\TestCase;

final class PricesFileTest extends TestCase
{
    public function testParsesFixture(): void
    {
        $file = PricesFile::fromFile(__DIR__ . '/../../fixtures/prices.json');

        self::assertCount(2, $file->providers());
        self::assertCount(3, $file->models());
        self::assertCount(4, $file->prices());

        $codes = array_map(static fn (ParsedProvider $p): string => $p->code(), $file->providers());
        self::assertSame(['prism', 'sfera'], $codes);

        $prism = $file->providers()[0];
        self::assertNotNull($prism->offPeak());
        self::assertSame('0.5', $prism->offPeak()->multiplier());
    }

    public function testPriceStructure(): void
    {
        $file = PricesFile::fromFile(__DIR__ . '/../../fixtures/prices.json');

        $unified = null;
        foreach ($file->prices() as $price) {
            if ($price['provider'] === 'sfera') {
                $unified = $price;
            }
        }

        self::assertNotNull($unified);
        self::assertSame('per_1m_tokens_unified', $unified['unit']);
        self::assertSame('2.400000', $unified['rates']['any']);
        self::assertSame('2026-08-01T00:00:00Z', $unified['valid_from']->format('Y-m-d\TH:i:s\Z'));
    }

    public function testMissingFileIsRejected(): void
    {
        $this->expectException(ImportException::class);
        PricesFile::fromFile(__DIR__ . '/nope.json');
    }

    public function testProviderRequiresName(): void
    {
        $this->expectException(ImportException::class);
        PricesFile::fromArray([
            'providers' => [['code' => 'p']],
        ]);
    }

    public function testInvalidTimezoneIsRejected(): void
    {
        $this->expectException(ImportException::class);
        PricesFile::fromArray([
            'providers' => [['code' => 'p', 'name' => 'P']],
            'models' => [['provider' => 'p', 'code' => 'm']],
            'prices' => [[
                'provider' => 'p',
                'model' => 'm',
                'valid_from' => '2026-08-01T02:00:00+02:00',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens_unified',
                'rates' => ['any' => '1'],
            ]],
        ]);
    }
}
