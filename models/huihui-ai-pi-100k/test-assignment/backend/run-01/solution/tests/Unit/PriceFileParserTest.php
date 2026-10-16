<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Ingest\PriceFileParser;
use PHPUnit\Framework\TestCase;

final class PriceFileParserTest extends TestCase
{
    private PriceFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PriceFileParser();
    }

    public function testFixtureFile(): void
    {
        $file = $this->parser->parse(dirname(__DIR__, 2) . '/fixtures/prices.json');
        self::assertCount(2, $file->providers);
        self::assertCount(3, $file->models); // lumen-8, lumen-mini, gyro-pro
        self::assertCount(4, $file->prices);

        // off_peak prism: 16:30..00:30 x0.5
        $prism = $file->providers['prism'];
        self::assertNotNull($prism->offPeak);
        self::assertSame(990, $prism->offPeak->minutesStart());
        self::assertSame(30, $prism->offPeak->minutesEnd());
        self::assertSame('0.500000', $prism->offPeak->multiplier());

        // sfera: off_peak явная null
        $sfera = $file->providers['sfera'];
        self::assertNull($sfera->offPeak);
        self::assertTrue($sfera->offPeakSpecified);
    }

    public function testInvalidShape(): void
    {
        $dir = sys_get_temp_dir() . '/billing_test_' . uniqid();
        @mkdir($dir);
        file_put_contents($dir . '/bad.json', json_encode(['not' => 'an-array']));
        $this->expectException(DomainException::class);
        $this->parser->parse($dir . '/bad.json');
    }
}
