<?php

declare(strict_types=1);

namespace App\Tests\Import;

use App\Domain\OffPeakWindow;
use App\Domain\PricingUnit;
use App\Exception\ImportException;
use App\Import\PriceFileParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PriceFileParserTest extends TestCase
{
    public function testParsesFixtureFile(): void
    {
        $file = PriceFileParser::parseFile(self::fixturePath());

        self::assertCount(2, $file->providers);
        self::assertCount(3, $file->models);
        self::assertCount(4, $file->prices);

        $prism = $file->providers[0];
        self::assertSame('prism', $prism->code);
        self::assertInstanceOf(OffPeakWindow::class, $prism->offPeak);
        self::assertSame(16 * 3600 + 30 * 60, $prism->offPeak->startSeconds());
        self::assertSame(30 * 60, $prism->offPeak->endSeconds());
        self::assertSame('0.5', $prism->offPeak->multiplier());

        // строка с OLD-ценами для lumen-8
        $old = null;
        foreach ($file->prices as $row) {
            if ($row->modelCode === 'lumen-8' && $row->unit === PricingUnit::PerMillionPerKind
                && $row->validFrom->format('Y-m-d') === '2026-08-01'
            ) {
                $old = $row;
            }
        }

        self::assertNotNull($old);
        self::assertSame(
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            $old->rates,
        );

        // unified-схема для gyro-pro и ключ «any»
        $unified = null;
        foreach ($file->prices as $row) {
            if ($row->modelCode === 'gyro-pro') {
                $unified = $row;
            }
        }

        self::assertNotNull($unified);
        self::assertSame(PricingUnit::PerMillionUnified, $unified->unit);
        self::assertSame(['any' => '2.400000'], $unified->rates);
    }

    public function testMinimalCorrectionFile(): void
    {
        // в файле ретро-коррекции providers/models допустимо не указывать
        $path = self::tempFile(json_encode([
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-08-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75'],
            ]],
        ]));

        try {
            $file = PriceFileParser::parseFile($path);

            self::assertSame([], $file->providers);
            self::assertSame([], $file->models);
            self::assertCount(1, $file->prices);
            self::assertSame('3.0', $file->prices[0]->rates['input']);
        } finally {
            self::unlinkQuietly($path);
        }
    }

    public function testRatesMustBeDecimalStrings(): void
    {
        $path = self::tempFile(json_encode([
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-08-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => 3.0, 'output' => 12, 'cache_read' => 0.3, 'cache_write' => 3.75],
            ]],
        ]));

        try {
            $this->expectException(ImportException::class);

            PriceFileParser::parseFile($path);
        } finally {
            self::unlinkQuietly($path);
        }
    }

    #[DataProvider('invalidPriceFile')]
    public function testInvalidPriceFileRejected(array $data, string $why): void
    {
        $this->expectException(ImportException::class);

        $path = self::tempFile(json_encode($data, JSON_UNESCAPED_UNICODE));
        try {
            PriceFileParser::parseFile($path);
        } finally {
            self::unlinkQuietly($path);
        }
    }

    /**
     * @return iterable<string, array{array, string}>
     */
    public static function invalidPriceFile(): iterable
    {
        yield 'неизвестная схема тарификации' => [[
            'prices' => [[
                'provider' => 'p', 'model' => 'm', 'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD', 'unit' => 'per_request', 'rates' => ['any' => '1'],
            ]],
        ], 'unit'];

        yield 'не хватает ставок' => [[
            'prices' => [[
                'provider' => 'p', 'model' => 'm', 'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD', 'unit' => 'per_1m_tokens', 'rates' => ['input' => '3'],
            ]],
        ], 'rates'];

        yield 'некорректный valid_from' => [[
            'prices' => [[
                'provider' => 'p', 'model' => 'm', 'valid_from' => '01.01.2026',
                'currency' => 'USD', 'unit' => 'per_1m_tokens_unified', 'rates' => ['any' => '1'],
            ]],
        ], 'valid_from'];

        yield 'off_peak: не время суток' => [[
            'providers' => [['code' => 'p', 'name' => 'P', 'off_peak' => ['window_start' => '25:00', 'window_end' => '02:00', 'multiplier' => '0.5']]],
        ], 'off_peak'];
    }

    public function testMissingFileRejected(): void
    {
        $this->expectException(ImportException::class);

        PriceFileParser::parseFile('/dev/null/definitely/missing.json');
    }

    private static function fixturePath(): string
    {
        return dirname(__DIR__, 2) . '/fixtures/prices.json';
    }

    private static function tempFile(string $json): string
    {
        $path = tempnam(sys_get_temp_dir(), 'prices-') . '.json';
        file_put_contents($path, $json);

        return $path;
    }

    private static function unlinkQuietly(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
